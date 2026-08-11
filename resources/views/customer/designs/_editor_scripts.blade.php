@php
    $isPanjang = \Illuminate\Support\Str::contains(strtolower($produk->nama_produk), 'panjang');
    $mockupBase = match($produk->jenis_produk) {
        'kaos' => $isPanjang ? 'kaos_panjang' : 'kaos',
        'hoodie' => 'hoodie',
        'polo' => 'polo',
        'seragam' => 'seragam',
        default => 'kaos'
    };
@endphp
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function initFabricEditor() {
  try {
    fabric.devicePixelRatio = (window.devicePixelRatio && window.devicePixelRatio > 2) ? window.devicePixelRatio : 3;

    const canvasFront = new fabric.Canvas('tshirt-canvas-front', { preserveObjectStacking: true, selection: true, enableRetinaScaling: true, imageSmoothingEnabled: true });
    const canvasBack = new fabric.Canvas('tshirt-canvas-back', { preserveObjectStacking: true, selection: true, enableRetinaScaling: true, imageSmoothingEnabled: true });
    let canvasLeft = null, canvasRight = null;
    if (document.getElementById('tshirt-canvas-left')) {
        canvasLeft = new fabric.Canvas('tshirt-canvas-left', { preserveObjectStacking: true, selection: true });
        canvasRight = new fabric.Canvas('tshirt-canvas-right', { preserveObjectStacking: true, selection: true });
    }
    window.activeCanvas = canvasFront;

    // Load saved data if exists
    const savedData = {
        front: {!! $desainRevisi && $desainRevisi->canvas_front ? json_encode($desainRevisi->canvas_front) : 'null' !!},
        back: {!! $desainRevisi && $desainRevisi->canvas_back ? json_encode($desainRevisi->canvas_back) : 'null' !!},
        left: {!! $desainRevisi && $desainRevisi->canvas_left ? json_encode($desainRevisi->canvas_left) : 'null' !!},
        right: {!! $desainRevisi && $desainRevisi->canvas_right ? json_encode($desainRevisi->canvas_right) : 'null' !!}
    };

    window.isLoadingJSON = false;
    function safeLoadCanvas(canvas, jsonStr) {
        if (!jsonStr || jsonStr === 'null') return;
        try {
            const jsonObj = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
            window.isLoadingJSON = true;
            canvas.loadFromJSON(jsonObj, function() {
                canvas.renderAll();
                if(typeof window.renderLayersList === 'function') window.renderLayersList();
                if(typeof window.saveHistory === 'function') window.saveHistory();
                window.isLoadingJSON = false;
            });
        } catch (e) {
            console.error("Error loading canvas JSON", e);
            window.isLoadingJSON = false;
        }
    }

    safeLoadCanvas(canvasFront, savedData.front);
    safeLoadCanvas(canvasBack, savedData.back);
    if(canvasLeft) {
        safeLoadCanvas(canvasLeft, savedData.left);
        safeLoadCanvas(canvasRight, savedData.right);
    }

    // History states
    window.historyStates = {
        front: { undo: [], redo: [] },
        back: { undo: [], redo: [] },
        left: { undo: [], redo: [] },
        right: { undo: [], redo: [] }
    };
    window.isHistoryAction = false;

    window.saveHistory = function() {
        if(window.isHistoryAction) return;
        let side = 'front';
        if(window.activeCanvas === canvasBack) side = 'back';
        else if(window.activeCanvas === canvasLeft) side = 'left';
        else if(window.activeCanvas === canvasRight) side = 'right';

        if(window.activeCanvas) {
            const json = window.activeCanvas.toJSON(['customType', 'sablonSize']);
            window.historyStates[side].undo.push(JSON.stringify(json));
            window.historyStates[side].redo = []; // clear redo on new action
            if(typeof window.updateHistoryButtons === 'function') window.updateHistoryButtons();
        }
    };

    window.undoHistory = function() {
        let side = 'front';
        if(window.activeCanvas === canvasBack) side = 'back';
        else if(window.activeCanvas === canvasLeft) side = 'left';
        else if(window.activeCanvas === canvasRight) side = 'right';

        if(window.historyStates[side].undo.length > 0) {
            window.isHistoryAction = true;
            const currentJson = window.activeCanvas.toJSON(['customType', 'sablonSize']);
            window.historyStates[side].redo.push(JSON.stringify(currentJson));
            
            const previousState = window.historyStates[side].undo.pop();
            window.activeCanvas.loadFromJSON(previousState, function() {
                window.activeCanvas.renderAll();
                window.isHistoryAction = false;
                if(typeof window.recalculateTotalPrice === 'function') window.recalculateTotalPrice();
                if(typeof window.renderLayersList === 'function') window.renderLayersList();
                if(typeof window.updateHistoryButtons === 'function') window.updateHistoryButtons();
            });
        }
    };

    window.redoHistory = function() {
        let side = 'front';
        if(window.activeCanvas === canvasBack) side = 'back';
        else if(window.activeCanvas === canvasLeft) side = 'left';
        else if(window.activeCanvas === canvasRight) side = 'right';

        if(window.historyStates[side].redo.length > 0) {
            window.isHistoryAction = true;
            const currentJson = window.activeCanvas.toJSON(['customType', 'sablonSize']);
            window.historyStates[side].undo.push(JSON.stringify(currentJson));
            
            const nextState = window.historyStates[side].redo.pop();
            window.activeCanvas.loadFromJSON(nextState, function() {
                window.activeCanvas.renderAll();
                window.isHistoryAction = false;
                if(typeof window.recalculateTotalPrice === 'function') window.recalculateTotalPrice();
                if(typeof window.renderLayersList === 'function') window.renderLayersList();
                if(typeof window.updateHistoryButtons === 'function') window.updateHistoryButtons();
            });
        }
    };

    window.updateHistoryButtons = function() {
        let side = 'front';
        if(window.activeCanvas === canvasBack) side = 'back';
        else if(window.activeCanvas === canvasLeft) side = 'left';
        else if(window.activeCanvas === canvasRight) side = 'right';

        const btnUndo = document.getElementById('btnUndoAction');
        const btnRedo = document.getElementById('btnRedoAction');
        if(btnUndo) {
            if(window.historyStates[side].undo.length > 0) { btnUndo.classList.remove('opacity-50', 'cursor-not-allowed'); btnUndo.disabled = false; }
            else { btnUndo.classList.add('opacity-50', 'cursor-not-allowed'); btnUndo.disabled = true; }
        }
        if(btnRedo) {
            if(window.historyStates[side].redo.length > 0) { btnRedo.classList.remove('opacity-50', 'cursor-not-allowed'); btnRedo.disabled = false; }
            else { btnRedo.classList.add('opacity-50', 'cursor-not-allowed'); btnRedo.disabled = true; }
        }
    };
    if (typeof initAligningGuidelines === 'function') {
        initAligningGuidelines(canvasFront);
        initAligningGuidelines(canvasBack);
        if (canvasLeft) initAligningGuidelines(canvasLeft);
        if (canvasRight) initAligningGuidelines(canvasRight);
    }

    window._desktopZoom = 1.0;
    window._mockupPanX = 0;
    window._mockupPanY = 0;

    // ==========================================================
    // SPACE PAN MODE — uses pointer-events:none on upperCanvasEl
    // and global class on body for uniform grab/grabbing cursor
    // ==========================================================
    const _panCanvases = [canvasFront, canvasBack, canvasLeft, canvasRight].filter(Boolean);
    let _panIsActive   = false;  // Space is held down
    let _panIsDragging = false;  // mouse is pressed during pan
    let _panLastX = 0, _panLastY = 0;
    const _panWrapper = document.getElementById('canvasScalerWrapper');
    const _panMockup  = document.getElementById('mockupContainer');

    function _panActivate() {
        if (_panIsActive) return;
        _panIsActive = true;
        document.body.classList.add('is-panning');
        _panCanvases.forEach(function(c) {
            c.selection = false;
            if (c.upperCanvasEl) {
                c.upperCanvasEl.style.pointerEvents = 'none';
            }
        });
    }

    function _panDeactivate() {
        if (!_panIsActive && !_panIsDragging) return;
        _panIsActive   = false;
        _panIsDragging = false;
        document.body.classList.remove('is-panning', 'is-panning-dragging');
        _panCanvases.forEach(function(c) {
            c.selection = true;
            if (c.upperCanvasEl) {
                c.upperCanvasEl.style.pointerEvents = '';
            }
        });
    }

    // Mousedown — on WINDOW capture, fires regardless of which child is the event target
    window.addEventListener('mousedown', function(e) {
        if (!_panIsActive && e.button !== 1) return;
        // Only pan when click is inside the canvas wrapper
        if (_panWrapper && !_panWrapper.contains(e.target) && e.target !== _panWrapper) return;
        _panIsDragging = true;
        _panLastX = e.clientX;
        _panLastY = e.clientY;
        document.body.classList.add('is-panning-dragging');
        e.preventDefault();
    }, { capture: true });

    // Mousemove — on window so we track even if mouse leaves wrapper
    window.addEventListener('mousemove', function(e) {
        if (!_panIsDragging || !_panMockup) return;
        var dx = e.clientX - _panLastX;
        var dy = e.clientY - _panLastY;
        window._mockupPanX = (window._mockupPanX || 0) + dx;
        window._mockupPanY = (window._mockupPanY || 0) + dy;
        _panLastX = e.clientX;
        _panLastY = e.clientY;
        if (typeof window.setupMobileCanvasScaler === 'function') {
            window.setupMobileCanvasScaler();
        }
    });

    // Mouseup — stop dragging, restore grab cursor if Space still held
    window.addEventListener('mouseup', function() {
        if (!_panIsDragging) return;
        _panIsDragging = false;
        document.body.classList.remove('is-panning-dragging');
        if (_panIsActive) {
            document.body.classList.add('is-panning');
        }
    });

    // Window blur — ensure pan mode is deactivated if window loses focus
    window.addEventListener('blur', _panDeactivate);
    
    // Mobile Canvas Scaling
    window.setupMobileCanvasScaler = function() {
        const mockupContainer = document.getElementById('mockupContainer');
        const scalerWrapper = document.getElementById('canvasScalerWrapper');
        if(!mockupContainer || !scalerWrapper) return;
        
        let scale = 1.0;
        let pX = 0;
        let pY = 0;
        
        if(window.innerWidth < 768) {
            const availableWidth = scalerWrapper.clientWidth - 32;
            const availableHeight = scalerWrapper.clientHeight - 32;
            
            if (availableWidth > 0 && availableHeight > 0) {
                const scaleW = availableWidth / 480;
                const scaleH = availableHeight / 600;
                scale = Math.min(scaleW, scaleH, 1.0);
            }
        }
        
        // Multiply by desktop zoom if applicable
        if (window.innerWidth >= 768) {
            scale = window._desktopZoom;
        }
        
        pX = window._mockupPanX;
        pY = window._mockupPanY;
        
        mockupContainer.style.transform = `translate(${pX}px, ${pY}px) scale(${scale})`;
    };
    
    
    window.addEventListener('resize', window.setupMobileCanvasScaler);
    window.addEventListener('orientationchange', () => setTimeout(window.setupMobileCanvasScaler, 200));
    setTimeout(window.setupMobileCanvasScaler, 100);
    
    if (window.ResizeObserver) {
        const wrapper = document.getElementById('canvasScalerWrapper');
        if(wrapper) new ResizeObserver(() => window.setupMobileCanvasScaler()).observe(wrapper);
    }

    window.switchCanvasSide = function(side) {
        if(window.activeCanvas) { window.activeCanvas.discardActiveObject(); window.activeCanvas.renderAll(); }
        if (typeof hideControls === 'function') hideControls();
        if(side === 'front') window.activeCanvas = canvasFront;
        else if(side === 'back') window.activeCanvas = canvasBack;
        else if(side === 'left') window.activeCanvas = canvasLeft;
        else if(side === 'right') window.activeCanvas = canvasRight;
        if(window.activeCanvas) {
            window.activeCanvas.calcOffset();
            window.activeCanvas.renderAll();
        }
        if (typeof window.renderLayersList === 'function') window.renderLayersList();
        if (typeof window.updateHistoryButtons === 'function') window.updateHistoryButtons();
        if (typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
    };

    // Generate preview thumbnails for Step 4
    const mockupFrontUrl = '{{ asset("images/mockups/" . $mockupBase . ".png") }}';
    const mockupBackUrl = '{{ asset("images/mockups/" . $mockupBase . "_belakang.png") }}';
    const mockupLeftUrl = '{{ asset("images/mockups/" . $mockupBase . "_samping_kiri.png") }}';
    const mockupRightUrl = '{{ asset("images/mockups/" . $mockupBase . "_samping_kanan.png") }}';

    function compositePreview(targetImgId, fabricCanvas, mockupUrl, hasDesign) {
        const targetImg = document.getElementById(targetImgId);
        if(!targetImg) return;

        const W = 300, H = 375;
        const offscreen = document.createElement('canvas');
        offscreen.width = W; offscreen.height = H;
        const ctx = offscreen.getContext('2d');

        // Step A: fill background with selected base color
        const baseColor = window.activeBaseColorLocal || '#ffffff';
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        // Step B: Load mockup shirt image, tint it, then overlay design
        const mockImg = new Image();
        mockImg.crossOrigin = 'anonymous';
        mockImg.onload = function() {
            // Draw colored tshirt (multiply blend)
            const tintCanvas = document.createElement('canvas');
            tintCanvas.width = W; tintCanvas.height = H;
            const tCtx = tintCanvas.getContext('2d');
            
            // Fill with base color
            tCtx.fillStyle = baseColor;
            tCtx.fillRect(0, 0, W, H);
            
            // Multiply blend the mockup
            tCtx.globalCompositeOperation = 'multiply';
            const padding = 20;
            const scale = Math.min((W - padding*2) / mockImg.width, (H - padding*2) / mockImg.height);
            const dw = mockImg.width * scale;
            const dh = mockImg.height * scale;
            const dx = (W - dw) / 2;
            const dy = (H - dh) / 2;
            tCtx.drawImage(mockImg, dx, dy, dw, dh);
            
            // Use mockup as mask (destination-in)
            tCtx.globalCompositeOperation = 'destination-in';
            tCtx.drawImage(mockImg, dx, dy, dw, dh);
            tCtx.globalCompositeOperation = 'source-over';

            // Draw tinted mockup onto main canvas
            ctx.drawImage(tintCanvas, 0, 0);

            // Step C: Overlay the Fabric.js design
            if(hasDesign && fabricCanvas && fabricCanvas.getObjects().length > 0) {
                const designSrc = fabricCanvas.toDataURL({ format: 'png', multiplier: 2 });
                const designImg = new Image();
                designImg.onload = function() {
                    // Position design in the center area of the shirt
                    const designScale = 0.45;
                    const ddw = W * designScale;
                    const ddh = H * designScale;
                    const ddx = (W - ddw) / 2;
                    const ddy = H * 0.22;
                    ctx.drawImage(designImg, ddx, ddy, ddw, ddh);
                    targetImg.src = offscreen.toDataURL('image/png');
                };
                designImg.src = designSrc;
            } else if(!hasDesign) {
                // No design on this side
                ctx.fillStyle = 'rgba(148,163,184,0.5)';
                ctx.font = 'bold 13px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Tidak ada desain', W/2, H - 30);
                targetImg.src = offscreen.toDataURL('image/png');
            } else {
                targetImg.src = offscreen.toDataURL('image/png');
            }
        };
        mockImg.onerror = function() {
            // Fallback: just draw raw canvas
            if(fabricCanvas && fabricCanvas.getObjects().length > 0) {
                targetImg.src = fabricCanvas.toDataURL({ format: 'png', multiplier: 2 });
            }
        };
        mockImg.src = mockupUrl;
    }

    window.generatePreviews = function() {
        try {
            compositePreview('preview-front', canvasFront, mockupFrontUrl, true);
            compositePreview('preview-back', canvasBack, mockupBackUrl, canvasBack && canvasBack.getObjects().length > 0);
            if(document.getElementById('preview-left')) {
                compositePreview('preview-left', canvasLeft, mockupLeftUrl, canvasLeft && canvasLeft.getObjects().length > 0);
                compositePreview('preview-right', canvasRight, mockupRightUrl, canvasRight && canvasRight.getObjects().length > 0);
            }
        } catch(e) { console.warn('Preview generation error:', e); }
    };

    // Watch for step changes to generate previews
    const rootEl = document.getElementById('editor-alpine') || document.querySelector('[x-data]');
    if(rootEl && window.Alpine) {
        Alpine.effect(() => {
            const data = Alpine.$data(rootEl);
            if(data.currentStep === data.totalSteps) { setTimeout(() => window.generatePreviews(), 300); }
        });
    }

    const printArea = window.printAreaDims ? window.printAreaDims['front'] : { top: 120, left: 130, width: 220, height: 320 };
    const editorControls = document.getElementById('editorControls');
    const deleteObjBtn = document.getElementById('deleteObjBtn');
    const bringForwardBtn = document.getElementById('bringForwardBtn');
    const sendBackwardBtn = document.getElementById('sendBackwardBtn');
    const textControls = document.getElementById('textControls');
    const fontFamilyControl = document.getElementById('fontFamilyControl');
    const textColorControl = document.getElementById('textColorControl');
    const textStrokeColor = document.getElementById('textStrokeColor');
    const textStrokeWidth = document.getElementById('textStrokeWidth');
    const textShadowToggle = document.getElementById('textShadowToggle');
    const imageControls = document.getElementById('imageControls');
    const removeBgBtn = document.getElementById('removeBgBtn');
    const removeColorTarget = document.getElementById('removeColorTarget');
    const removeColorTolerance = document.getElementById('removeColorTolerance');
    const detectBgColorBtn = document.getElementById('detectBgColorBtn');
    const resetBgBtn = document.getElementById('resetBgBtn');
    const svgControls = document.getElementById('svgControls');
    const svgColorControl = document.getElementById('svgColorControl');

    // Fabric global styles
    fabric.Object.prototype.transparentCorners = false;
    fabric.Object.prototype.cornerColor = '#ffffff';
    fabric.Object.prototype.cornerStrokeColor = '#bae6fd';
    fabric.Object.prototype.borderColor = '#0284c7';
    fabric.Object.prototype.cornerSize = window.innerWidth < 768 ? 24 : 14;
    fabric.Object.prototype.touchCornerSize = 32;
    fabric.Object.prototype.cornerStyle = 'circle';
    fabric.Object.prototype.padding = window.innerWidth < 768 ? 16 : 10;
    fabric.Object.prototype.borderDashArray = [4, 4];
    fabric.Object.prototype.objectCaching = false;
    fabric.Image.prototype.objectCaching = false;
    fabric.Image.prototype.noScaleCache = false;
    if(fabric.Object.prototype.setControlsVisibility) {
        fabric.Object.prototype.setControlsVisibility({ mt: false, mb: false, ml: false, mr: false });
    }

    // Add Text
    const addTextBtn = document.getElementById('addTextBtn');
    if(addTextBtn) {
        addTextBtn.addEventListener('click', function() {
            let sideName = 'front';
            try { const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]'); if(el && window.Alpine && window.Alpine.$data) sideName = window.Alpine.$data(el).activeSide || 'front'; } catch(e) {}
            const dims = window.printAreaDims[sideName] || window.printAreaDims['front'];
            const w = dims ? dims.width : window.activeCanvas.width;
            
            const text = new fabric.IText('Teks Anda', { 
                left: w / 2, 
                top: 40, 
                originX: 'center',
                originY: 'center',
                textAlign: 'center',
                fontFamily: 'Arial', 
                fill: '#000000', 
                fontSize: 40, 
                fontWeight: 'bold', 
                sablonSize: 'a5' 
            });
            window.activeCanvas.add(text);
            window.activeCanvas.setActiveObject(text);
            window.activeCanvas.requestRenderAll();
        });
    }

    // Font controls
    if(fontFamilyControl) fontFamilyControl.addEventListener('change', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('fontFamily', this.value); window.activeCanvas.renderAll(); } });
    if(textColorControl) textColorControl.addEventListener('input', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('fill', this.value); window.activeCanvas.renderAll(); } });
    if(textStrokeColor) textStrokeColor.addEventListener('input', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set({ stroke: this.value, strokeWidth: parseInt(textStrokeWidth.value) }); window.activeCanvas.renderAll(); } });
    if(textStrokeWidth) textStrokeWidth.addEventListener('input', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set({ stroke: textStrokeColor.value, strokeWidth: parseInt(this.value) }); window.activeCanvas.renderAll(); } });
    if(textShadowToggle) textShadowToggle.addEventListener('change', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('shadow', this.checked ? new fabric.Shadow({ color: 'rgba(0,0,0,0.6)', blur: 4, offsetX: 2, offsetY: 2 }) : null); window.activeCanvas.renderAll(); } });

    // Layer management
    if(bringForwardBtn) bringForwardBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o) window.activeCanvas.bringForward(o); });
    if(sendBackwardBtn) sendBackwardBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o) window.activeCanvas.sendBackwards(o); });

    // Add image/SVG to canvas
    function addImageToCanvas(url) {
        let sideName = 'front';
        try { const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]'); if(el && window.Alpine && window.Alpine.$data) sideName = window.Alpine.$data(el).activeSide || 'front'; } catch(e) {}
        const dims = window.printAreaDims[sideName] || window.printAreaDims['front'];
        const pa_width = dims ? dims.width : window.activeCanvas.width;
        const imgEl = new Image();
        imgEl.crossOrigin = 'anonymous';
        imgEl.onload = function() {
            const img = new fabric.Image(imgEl);
            if(img.width > pa_width) img.scaleToWidth(pa_width - 20);
            else if(img.width < 40) img.scaleToWidth(80);
            img.set({ left: 10, top: 10, objectCaching: false });
            img.customType = 'custom-image';
            img.sablonSize = 'a4';
            window.activeCanvas.add(img); window.activeCanvas.setActiveObject(img); window.activeCanvas.requestRenderAll();
        };
        imgEl.onerror = function() {
            const imgEl2 = new Image();
            imgEl2.onload = function() { const img = new fabric.Image(imgEl2); if(img.width > pa_width) img.scaleToWidth(pa_width - 20); else if(img.width < 40) img.scaleToWidth(80); img.set({ left: 10, top: 10, objectCaching: false }); img.customType = 'custom-image'; img.sablonSize = 'a4'; window.activeCanvas.add(img); window.activeCanvas.setActiveObject(img); window.activeCanvas.requestRenderAll(); };
            imgEl2.src = url;
        };
        imgEl.src = url;
    }

    // Add SVG to canvas
    function addSVGToCanvas(url) {
        fabric.loadSVGFromURL(url, function(objects, options) {
            if(objects && objects.length > 0) {
                const group = fabric.util.groupSVGElements(objects, options);
                const targetSize = Math.min(60, window.activeCanvas.width - 20);
                group.scale(targetSize / Math.max(group.width || 1, group.height || 1));
                group.set({ left: 10, top: 10 }); 
                group.customType = 'custom-svg';
                group.sablonSize = 'a5';
                window.activeCanvas.add(group); window.activeCanvas.setActiveObject(group); window.activeCanvas.requestRenderAll();
            } else { addImageToCanvas(url); }
        }, null, { crossOrigin: 'anonymous' });
    }

    // Upload handler
    const imageLoader = document.getElementById('imageLoader');
    if(imageLoader) imageLoader.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        // Validasi format sesuai flowchart
        var validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format Tidak Valid',
                text: 'Harap unggah gambar dengan format PNG, JPG, atau SVG.',
                confirmButtonColor: '#4f46e5'
            });
            e.target.value = ''; // Reset input
            return;
        }

        var reader = new FileReader();
        reader.onload = function(event) {
            var imgObj = new Image(); imgObj.src = event.target.result;
            imgObj.onload = function() {
                var img = new fabric.Image(imgObj);
                if(img.width > window.activeCanvas.width) img.scaleToWidth(window.activeCanvas.width - 20);
                img.set({ left: 10, top: 10, objectCaching: false }); 
                img.customType = 'custom-image';
                img.sablonSize = 'a4';
                window.activeCanvas.add(img); window.activeCanvas.setActiveObject(img); window.activeCanvas.requestRenderAll();
            }
        };
        reader.readAsDataURL(file); e.target.value = '';
    });

    // Template click
    document.querySelectorAll('.template-item').forEach(item => { item.addEventListener('click', function() { addImageToCanvas(this.getAttribute('data-url')); }); });

    // Stickers
    const stickerSearchInput = document.getElementById('stickerSearchInput');
    const searchStickerBtn = document.getElementById('searchStickerBtn');
    const stickersContainer = document.getElementById('stickersContainer');

    function loadStickers(query = 'heart') {
        if(!stickersContainer) return;
        stickersContainer.innerHTML = '<div class="col-span-2 text-center py-5"><svg class="animate-spin h-5 w-5 text-red-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-red-600 block font-bold mt-1">Memuat ikon...</span></div>';
        fetch(`/customer/api/stickers?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(json => {
                stickersContainer.innerHTML = '';
                const icons = json.data || [];
                if(icons.length > 0) {
                    icons.forEach(icon => {
                        const btn = document.createElement('div');
                        btn.className = 'relative bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-red-400 hover:shadow-md transition-all duration-200 flex items-center justify-center w-full h-24 group overflow-hidden';
                        btn.title = icon.name;
                        btn.innerHTML = `<img src="${icon.url}" loading="lazy" class="w-14 h-14 object-contain group-hover:scale-110 transition-transform duration-200" onerror="this.parentElement.style.display='none'">`;
                        btn.addEventListener('click', function() { icon.source === 'Iconify' ? addSVGToCanvas(icon.url) : addImageToCanvas(icon.url); });
                        stickersContainer.appendChild(btn);
                    });
                } else {
                    stickersContainer.innerHTML = '<div class="col-span-2 text-center text-xs text-slate-500 py-6">🔍 Ikon tidak ditemukan.</div>';
                }
            }).catch(() => showFallbackStickers());
    }
    function showFallbackStickers() {
        if(!stickersContainer) return;
        const fallback = [
            { name: 'bintang', url: 'https://api.iconify.design/twemoji/star.svg' },
            { name: 'hati', url: 'https://api.iconify.design/twemoji/red-heart.svg' },
            { name: 'api', url: 'https://api.iconify.design/twemoji/fire.svg' },
            { name: 'mahkota', url: 'https://api.iconify.design/twemoji/crown.svg' },
            { name: 'petir', url: 'https://api.iconify.design/twemoji/high-voltage.svg' },
            { name: 'roket', url: 'https://api.iconify.design/twemoji/rocket.svg' },
        ];
        stickersContainer.innerHTML = '<div class="col-span-2 text-[10px] text-slate-400 text-center mb-2 font-bold">Ikon Default</div>';
        fallback.forEach(ic => {
            const btn = document.createElement('div');
            btn.className = 'relative bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-red-400 hover:shadow-md transition-all duration-200 flex items-center justify-center w-full h-24 group overflow-hidden';
            btn.innerHTML = `<img src="${ic.url}" loading="lazy" class="w-14 h-14 object-contain group-hover:scale-110 transition-transform" onerror="this.parentElement.style.display='none'">`;
            btn.addEventListener('click', () => addSVGToCanvas(ic.url));
            stickersContainer.appendChild(btn);
        });
    }
    try {
        loadStickers('star');
        if(searchStickerBtn) searchStickerBtn.addEventListener('click', () => loadStickers(stickerSearchInput ? stickerSearchInput.value.trim() || 'star' : 'star'));
        if(stickerSearchInput) stickerSearchInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') loadStickers(stickerSearchInput.value.trim() || 'star'); });
    } catch(e) { console.error("Sticker Init Error:", e); }

    // Perhitungan Harga Sablon Dinamis Per Objek (Saat ini Dinonaktifkan)
    window.recalculateTotalPrice = function() {
        let totalDesignPrice = 0; // Pricing dinonaktifkan
        let breakdownTextFront = [];
        let breakdownTextBack = [];
        let breakdownTextLeft = [];
        let breakdownTextRight = [];

        if (canvasFront) {
            canvasFront.getObjects().forEach((obj, idx) => {
                let typeLabel = '';
                if(obj.type === 'i-text') typeLabel = `Teks (Font: ${obj.fontFamily || 'Default'})`;
                else if(obj.customType === 'custom-svg') typeLabel = 'Template/Stiker';
                else typeLabel = 'Gambar Upload';
                breakdownTextFront.push(`${typeLabel} #${idx+1}`);
            });
        }

        if (canvasBack) {
            canvasBack.getObjects().forEach((obj, idx) => {
                let typeLabel = '';
                if(obj.type === 'i-text') typeLabel = `Teks (Font: ${obj.fontFamily || 'Default'})`;
                else if(obj.customType === 'custom-svg') typeLabel = 'Template/Stiker';
                else typeLabel = 'Gambar Upload';
                breakdownTextBack.push(`${typeLabel} #${idx+1}`);
            });
        }

        if (canvasLeft) {
            canvasLeft.getObjects().forEach((obj, idx) => {
                let typeLabel = '';
                if(obj.type === 'i-text') typeLabel = `Teks (Font: ${obj.fontFamily || 'Default'})`;
                else if(obj.customType === 'custom-svg') typeLabel = 'Template/Stiker';
                else typeLabel = 'Gambar Upload';
                breakdownTextLeft.push(`${typeLabel} #${idx+1}`);
            });
        }

        if (canvasRight) {
            canvasRight.getObjects().forEach((obj, idx) => {
                let typeLabel = '';
                if(obj.type === 'i-text') typeLabel = `Teks (Font: ${obj.fontFamily || 'Default'})`;
                else if(obj.customType === 'custom-svg') typeLabel = 'Template/Stiker';
                else typeLabel = 'Gambar Upload';
                breakdownTextRight.push(`${typeLabel} #${idx+1}`);
            });
        }

        const breakdownContainer = document.getElementById('priceBreakdownContainer');
        if (breakdownContainer) {
            let html = '';
            if (breakdownTextFront.length > 0) {
                html += `<div class="mb-2"><span class="font-bold text-slate-700 block text-xs">Sisi Depan:</span>`;
                html += `<ul class="list-disc list-inside text-xs text-slate-500 pl-2">`;
                breakdownTextFront.forEach(item => { html += `<li>${item}</li>`; });
                html += `</ul></div>`;
            }
            if (breakdownTextBack.length > 0) {
                html += `<div class="mb-2"><span class="font-bold text-slate-700 block text-xs">Sisi Belakang:</span>`;
                html += `<ul class="list-disc list-inside text-xs text-slate-500 pl-2">`;
                breakdownTextBack.forEach(item => { html += `<li>${item}</li>`; });
                html += `</ul></div>`;
            }
            if (breakdownTextLeft.length > 0) {
                html += `<div class="mb-2"><span class="font-bold text-slate-700 block text-xs">Sisi Kiri:</span>`;
                html += `<ul class="list-disc list-inside text-xs text-slate-500 pl-2">`;
                breakdownTextLeft.forEach(item => { html += `<li>${item}</li>`; });
                html += `</ul></div>`;
            }
            if (breakdownTextRight.length > 0) {
                html += `<div class="mb-2"><span class="font-bold text-slate-700 block text-xs">Sisi Kanan:</span>`;
                html += `<ul class="list-disc list-inside text-xs text-slate-500 pl-2">`;
                breakdownTextRight.forEach(item => { html += `<li>${item}</li>`; });
                html += `</ul></div>`;
            }
            if (breakdownTextFront.length === 0 && breakdownTextBack.length === 0 && breakdownTextLeft.length === 0 && breakdownTextRight.length === 0) {
                html = `<p class="text-xs text-slate-400 italic">Belum ada objek sablon ditambahkan.</p>`;
            }
            breakdownContainer.innerHTML = html;
        }

        // Update DOM values
        const biayaDesainEls = document.querySelectorAll('.biaya-desain-val');
        biayaDesainEls.forEach(el => {
            el.textContent = `Rp ${totalDesignPrice.toLocaleString('id-ID')}`;
        });

        const hargaDasar = parseInt('{{ $produk->harga_dasar }}') || 0;
        const totalHargaEls = document.querySelectorAll('.total-harga-val');
        totalHargaEls.forEach(el => {
            el.textContent = `Rp ${(hargaDasar + totalDesignPrice).toLocaleString('id-ID')}`;
        });

        window.currentHargaDesain = totalDesignPrice;
        let desc = '';
        if(breakdownTextFront.length > 0) desc += 'Depan: ' + breakdownTextFront.join(', ') + '. ';
        if(breakdownTextBack.length > 0) desc += 'Belakang: ' + breakdownTextBack.join(', ') + '. ';
        if(breakdownTextLeft.length > 0) desc += 'Kiri: ' + breakdownTextLeft.join(', ') + '. ';
        if(breakdownTextRight.length > 0) desc += 'Kanan: ' + breakdownTextRight.join(', ') + '.';
        window.currentDetailSablon = desc;
    };

    window.updateSablonSizeButtons = function(size) {
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('border-red-500', 'bg-red-50', 'ring-2', 'ring-red-400/50');
            btn.classList.add('border-slate-200', 'bg-slate-50');
        });
        const activeBtn = document.getElementById('btnSize' + size.toUpperCase());
        if(activeBtn) {
            activeBtn.classList.remove('border-slate-200', 'bg-slate-50');
            activeBtn.classList.add('border-red-500', 'bg-red-50', 'ring-2', 'ring-red-400/50');
        }
    };

    window.resizeObjectToSablonSize = function(obj, size) {
        if (!obj) return;
        
        if (!size) size = obj.sablonSize || 'a5';
        obj.sablonSize = size;

        let sideName = 'front';
        try {
            const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]');
            if (el && window.Alpine && window.Alpine.$data) {
                sideName = window.Alpine.$data(el).activeSide || 'front';
            }
        } catch(e) {}
        
        const dims = (window.printAreaDims && window.printAreaDims[sideName]) || { width: 220, height: 320 };
        const pa_width = dims.width;
        const pa_height = dims.height;

        // Visual proportions relative to the print area bounds:
        // Asumsi standar lebar sablon adalah 28cm yang dipetakan ke pa_width (misal: 220px)
        // Maka 1 cm = pa_width / 28
        let px_per_cm = pa_width / 28;
        if (pa_width < 120) {
            // Untuk polo atau seragam, area cetak aslinya lebih kecil (sekitar 10-12 cm)
            px_per_cm = pa_width / 10;
        }

        let targetWidth, targetHeight;
        
        if (size === 'a4') { // Ukuran asli A4: ~20x25 cm
            targetWidth = 20 * px_per_cm;
            targetHeight = 25 * px_per_cm;
        } else if (size === 'a3') { // Ukuran asli A3: ~25x35 cm
            targetWidth = 25 * px_per_cm;
            targetHeight = 35 * px_per_cm;
        } else { // Ukuran A5 Logo: 10x10 cm
            targetWidth = 10 * px_per_cm;
            targetHeight = 10 * px_per_cm;
        }

        const margin = 10;
        targetWidth = Math.min(targetWidth, pa_width - margin);
        targetHeight = Math.min(targetHeight, pa_height - margin);

        const origWidth = obj.width;
        const origHeight = obj.height;

        if (origWidth && origHeight) {
            const scaleX = targetWidth / origWidth;
            const scaleY = targetHeight / origHeight;
            const scale = Math.min(scaleX, scaleY);
            
            obj.set({
                scaleX: scale,
                scaleY: scale,
                lockScalingX: false,
                lockScalingY: false,
                lockUniScaling: true
            });
            
            if (typeof obj.setControlsVisibility === 'function') {
                obj.setControlsVisibility({
                    mt: false, mb: false, ml: false, mr: false,
                    tl: true, tr: true, bl: true, br: true,
                    mtr: true
                });
            }
        }

        // Keep object within printable bounds
        let left = obj.left || 0;
        let top = obj.top || 0;
        const scaledWidth = (obj.width || 0) * (obj.scaleX || 1);
        const scaledHeight = (obj.height || 0) * (obj.scaleY || 1);
        const oX = obj.originX || 'left';
        const oY = obj.originY || 'top';
        
        if (oX === 'center') {
            if (left - scaledWidth/2 < 0) left = scaledWidth/2;
            if (left + scaledWidth/2 > pa_width) left = pa_width - scaledWidth/2;
        } else {
            if (left < 0) left = 0;
            if (left + scaledWidth > pa_width) left = pa_width - scaledWidth;
        }
        
        if (oY === 'center') {
            if (top - scaledHeight/2 < 0) top = scaledHeight/2;
            if (top + scaledHeight/2 > pa_height) top = pa_height - scaledHeight/2;
        } else {
            if (top < 0) top = 0;
            if (top + scaledHeight > pa_height) top = pa_height - scaledHeight;
        }
        
        obj.set({ left: left, top: top });
        obj.setCoords();
        if (obj.canvas) {
            obj.canvas.requestRenderAll();
        }
    };

    window.setObjectSablonSize = function(size) {
        const activeObj = window.activeCanvas.getActiveObject();
        if (activeObj) {
            window.resizeObjectToSablonSize(activeObj, size);
            window.updateSablonSizeButtons(size);
            window.recalculateTotalPrice();
        }
    };

    // Editor controls logic
    const canvasesToHandle = [canvasFront, canvasBack];
    if(canvasLeft) canvasesToHandle.push(canvasLeft);
    if(canvasRight) canvasesToHandle.push(canvasRight);

    function constrainObjectBounds(e) {
        const obj = e.target;
        if (!obj) return;
        
        let sideName = 'front';
        try {
            const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]');
            if (el && window.Alpine && window.Alpine.$data) {
                sideName = window.Alpine.$data(el).activeSide || 'front';
            }
        } catch(err) {}
        
        const dims = (window.printAreaDims && window.printAreaDims[sideName]) || { width: 220, height: 320 };
        const pa_width = dims.width;
        const pa_height = dims.height;

        obj.setCoords();
        const boundingRect = obj.getBoundingRect();

        let leftOffset = 0;
        let topOffset = 0;

        if (boundingRect.left < 0) {
            leftOffset = -boundingRect.left;
        } else if (boundingRect.left + boundingRect.width > pa_width) {
            leftOffset = pa_width - (boundingRect.left + boundingRect.width);
        }

        if (boundingRect.top < 0) {
            topOffset = -boundingRect.top;
        } else if (boundingRect.top + boundingRect.height > pa_height) {
            topOffset = pa_height - (boundingRect.top + boundingRect.height);
        }

        if (leftOffset !== 0 || topOffset !== 0) {
            if (!window._boundaryAlertShown) {
                window._boundaryAlertShown = true;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'warning',
                        title: 'Objek menyentuh batas area cetak',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
                
                // Flash print area
                const paBox = document.getElementById('printAreaBox');
                if (paBox) {
                    paBox.classList.add('border-red-500', 'bg-red-500/10');
                    setTimeout(() => {
                        paBox.classList.remove('border-red-500', 'bg-red-500/10');
                        window._boundaryAlertShown = false;
                    }, 1500);
                } else {
                    setTimeout(() => window._boundaryAlertShown = false, 2000);
                }
            }
        }

        if (leftOffset !== 0) obj.set('left', obj.left + leftOffset);
        if (topOffset !== 0) obj.set('top', obj.top + topOffset);
    }

    window.renderLayersList = function() {
        const container = document.getElementById('layersListContainer');
        if(!container) return;
        
        let canvas = window.activeCanvas;
        if(!canvas) return;
        
        const objects = canvas.getObjects();
        if(objects.length === 0) {
            container.innerHTML = '<div class="text-center text-xs text-slate-400 py-4 italic tracking-widest">Belum ada objek.</div>';
            return;
        }
        
        let html = '';
        for(let i = objects.length - 1; i >= 0; i--) {
            const obj = objects[i];
            let typeLabel = 'Objek';
            let iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>';
            
            if(obj.type === 'i-text') {
                typeLabel = 'Teks: ' + (obj.text ? obj.text.substring(0, 10) : '');
                iconSvg = '<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>';
            } else if(obj.customType === 'custom-svg') {
                typeLabel = 'Stiker';
                iconSvg = '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            } else if(obj.type === 'image') {
                typeLabel = 'Gambar Upload';
                iconSvg = '<svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
            }
            
            const isActive = canvas.getActiveObject() === obj;
            const bgClass = isActive ? 'bg-red-50 border-red-200 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50';
            
            html += `
            <div class="flex items-center justify-between p-2 rounded-xl border ${bgClass} transition mb-2">
                <div class="flex items-center gap-3 flex-1 cursor-pointer" onclick="if(typeof window.selectLayerIndex === 'function') window.selectLayerIndex(${i})">
                    <div class="p-2 bg-slate-100 rounded-lg">${iconSvg}</div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs font-bold text-slate-700 truncate">${typeLabel}</span>
                        <span class="text-[9px] text-slate-400">Lapisan ${i + 1}</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <button onclick="if(typeof window.toggleLockLayer === 'function') window.toggleLockLayer(${i})" class="p-2 ${obj.locked ? 'text-red-500' : 'text-slate-400 hover:text-slate-600'} rounded-lg transition shrink-0" title="${obj.locked ? 'Buka Kunci' : 'Kunci Lapisan'}">
                        ${obj.locked 
                            ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>'
                            : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>'}
                    </button>
                    <button onclick="if(typeof window.deleteLayerIndex === 'function') window.deleteLayerIndex(${i})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition shrink-0" title="Hapus Lapisan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>`;
        }
        container.innerHTML = html;
    };

    window.selectLayerIndex = function(i) {
        if(!window.activeCanvas) return;
        const objects = window.activeCanvas.getObjects();
        if(objects[i]) {
            window.activeCanvas.setActiveObject(objects[i]);
            window.activeCanvas.requestRenderAll();
            window.renderLayersList();
        }
    };

    window.toggleLockLayer = function(i) {
        if(!window.activeCanvas) return;
        const objects = window.activeCanvas.getObjects();
        if(objects[i]) {
            const obj = objects[i];
            obj.locked = !obj.locked;
            obj.set({
                selectable: !obj.locked,
                evented: !obj.locked,
                lockMovementX: obj.locked,
                lockMovementY: obj.locked,
                lockRotation: obj.locked,
                lockScalingX: obj.locked,
                lockScalingY: obj.locked,
                hasControls: !obj.locked
            });
            window.activeCanvas.discardActiveObject();
            window.activeCanvas.requestRenderAll();
            window.renderLayersList();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        }
    };

    window.deleteLayerIndex = function(i) {
        if(!window.activeCanvas) return;
        const objects = window.activeCanvas.getObjects();
        if(objects[i]) {
            window.activeCanvas.remove(objects[i]);
            window.activeCanvas.discardActiveObject();
            window.activeCanvas.requestRenderAll();
            window.renderLayersList();
            window.recalculateTotalPrice();
        }
    };

    canvasesToHandle.forEach(c => { 
        c.on('selection:created', showControls); 
        c.on('selection:updated', showControls); 
        c.on('selection:cleared', hideControls); 
        c.on('object:added', function(e) {
            const obj = e.target;
            if (obj && !window.isHistoryAction && !obj.isClone && !window.isLoadingJSON) {
                const size = obj.sablonSize || ((obj.type === 'i-text' || obj.customType === 'custom-svg') ? 'a5' : 'a4');
                window.resizeObjectToSablonSize(obj, size);
            }
            if (obj && obj.isClone) {
                delete obj.isClone;
            }
            if(typeof window.recalculateTotalPrice === 'function') window.recalculateTotalPrice();
            if(typeof window.renderLayersList === 'function') window.renderLayersList();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        });
        c.on('object:removed', function(e) {
            if(typeof window.recalculateTotalPrice === 'function') window.recalculateTotalPrice();
            if(typeof window.renderLayersList === 'function') window.renderLayersList();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        });
        c.on('object:modified', function(e) {
            if(typeof window.renderLayersList === 'function') window.renderLayersList();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        });
        c.on('object:moving', constrainObjectBounds);
        c.on('object:scaling', constrainObjectBounds);
    });

    // Workspace Wheel Zoom (attached directly to scaler wrapper)
    if (_panWrapper) {
        _panWrapper.addEventListener('wheel', function(e) {
            if (window.innerWidth < 768) return;
            e.preventDefault();
            
            var delta = e.deltaY;
            var oldZoom = window._desktopZoom || 1.0;
            var factor = delta < 0 ? 1.1 : 0.9;
            var newZoom = oldZoom * factor;
            
            if (newZoom > 3.5) newZoom = 3.5;
            if (newZoom < 0.4) newZoom = 0.4;
            
            if (newZoom !== oldZoom) {
                var rect = _panWrapper.getBoundingClientRect();
                var centerX = rect.left + rect.width / 2;
                var centerY = rect.top + rect.height / 2;
                var mouseX = e.clientX - centerX;
                var mouseY = e.clientY - centerY;
                
                var k = newZoom / oldZoom;
                window._mockupPanX = (window._mockupPanX || 0) - (k - 1) * (mouseX - (window._mockupPanX || 0));
                window._mockupPanY = (window._mockupPanY || 0) - (k - 1) * (mouseY - (window._mockupPanY || 0));
                
                window._desktopZoom = newZoom;
                if (typeof window.setupMobileCanvasScaler === 'function') {
                    window.setupMobileCanvasScaler();
                }
                var resetBtn = document.getElementById('zoomResetBtn');
                if (resetBtn) {
                    resetBtn.innerText = Math.round(window._desktopZoom * 100) + '%';
                }
            }
        }, { passive: false });
    }

    // Global Keyboard Shortcuts
    window.addEventListener('keydown', function(e) {
        if(e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        if(e.key === 'Delete' || e.key === 'Backspace') {
            const activeObj = window.activeCanvas.getActiveObject();
            if(activeObj && !activeObj.isEditing) {
                e.preventDefault();
                window.activeCanvas.remove(activeObj);
                window.activeCanvas.discardActiveObject();
                window.activeCanvas.requestRenderAll();
                if(typeof hideControls === 'function') hideControls();
            }
        }
        
        if(e.code === 'Space') {
            if (e.repeat) return;  // ignore key-hold repeats
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                const activeObj = window.activeCanvas ? window.activeCanvas.getActiveObject() : null;
                if (activeObj && activeObj.isEditing) {
                    return; // Allow typing spaces when text object is in edit mode
                }
                e.preventDefault();  // prevent page scroll on Space
                _panActivate();
            }
        }
        
        if(e.ctrlKey || e.metaKey) {
            if(e.key === 'z' || e.key === 'Z') {
                e.preventDefault();
                if(e.shiftKey) window.redoHistory();
                else window.undoHistory();
            }
            if(e.key === 'y' || e.key === 'Y') {
                e.preventDefault();
                window.redoHistory();
            }
            if(e.key === 'c' || e.key === 'C') {
                e.preventDefault();
                const activeObj = window.activeCanvas.getActiveObject();
                if(activeObj) {
                    activeObj.clone(function(cloned) {
                        window._clipboard = cloned;
                    }, ['customType', 'sablonSize']);
                }
            }
            if(e.key === 'v' || e.key === 'V') {
                e.preventDefault();
                if(!window._clipboard) return;
                window._clipboard.clone(function(clonedObj) {
                    window.activeCanvas.discardActiveObject();
                    clonedObj.set({
                        left: clonedObj.left + 10,
                        top: clonedObj.top + 10,
                        evented: true,
                    });
                    clonedObj.isClone = true;
                    if (clonedObj.type === 'activeSelection') {
                        clonedObj.canvas = window.activeCanvas;
                        clonedObj.forEachObject(function(obj) { obj.isClone = true; window.activeCanvas.add(obj); });
                        clonedObj.setCoords();
                    } else {
                        window.activeCanvas.add(clonedObj);
                    }
                    window._clipboard.top += 10;
                    window._clipboard.left += 10;
                    window.activeCanvas.setActiveObject(clonedObj);
                    window.activeCanvas.requestRenderAll();
                }, ['customType', 'sablonSize']);
            }
        }
    });

    window.addEventListener('keyup', function(e) {
        if(e.code === 'Space') {
            _panDeactivate();
        }
    });


    window.toggleMobileProperties = function() {
        if(!editorControls) return;
        if(editorControls.classList.contains('hidden')) {
            editorControls.classList.remove('hidden'); editorControls.classList.add('flex');
            const backdrop = document.getElementById('editorControlsBackdrop');
            if(backdrop) { backdrop.classList.remove('hidden'); backdrop.classList.add('block'); }
        } else {
            editorControls.classList.add('hidden'); editorControls.classList.remove('flex');
            const backdrop = document.getElementById('editorControlsBackdrop');
            if(backdrop) { backdrop.classList.add('hidden'); backdrop.classList.remove('block'); }
        }
    };

    function showControls(e) {
        if(typeof window.renderLayersList === 'function') window.renderLayersList();
        if(!editorControls) return;
        const mobileEditBtn = document.getElementById('mobileEditBtn');
        if(mobileEditBtn) mobileEditBtn.classList.remove('hidden');
        
        if(window.innerWidth >= 768) {
            editorControls.classList.remove('hidden'); editorControls.classList.add('flex');
            const backdrop = document.getElementById('editorControlsBackdrop');
            if(backdrop) { backdrop.classList.remove('hidden'); backdrop.classList.add('block'); }
        }

        const activeObj = (e && e.selected) ? e.selected[0] : window.activeCanvas.getActiveObject();
        if(!activeObj) return;
        textControls.classList.add('hidden'); textControls.classList.remove('flex');
        imageControls.classList.add('hidden'); imageControls.classList.remove('flex');
        svgControls.classList.add('hidden'); svgControls.classList.remove('flex');

        // Set active size buttons
        const size = activeObj.sablonSize || 'a5';
        window.updateSablonSizeButtons(size);
        
        const groupingControls = document.getElementById('groupingControls');
        const groupBtnEl = document.getElementById('groupBtn');
        const ungroupBtnEl = document.getElementById('ungroupBtn');

        if(groupingControls) {
            groupingControls.classList.add('hidden');
            if (activeObj.type === 'activeSelection') {
                groupingControls.classList.remove('hidden');
                if (groupBtnEl) { groupBtnEl.classList.remove('hidden'); groupBtnEl.classList.add('flex'); }
                if (ungroupBtnEl) { ungroupBtnEl.classList.add('hidden'); ungroupBtnEl.classList.remove('flex'); }
            } else if (activeObj.type === 'group' && activeObj.customType !== 'custom-svg') {
                groupingControls.classList.remove('hidden');
                if (groupBtnEl) { groupBtnEl.classList.add('hidden'); groupBtnEl.classList.remove('flex'); }
                if (ungroupBtnEl) { ungroupBtnEl.classList.remove('hidden'); ungroupBtnEl.classList.add('flex'); }
            }
        }

        if(activeObj.type === 'i-text') {
            textControls.classList.remove('hidden'); textControls.classList.add('flex');
            const textValueControl = document.getElementById('textValueControl');
            if(textValueControl) textValueControl.value = activeObj.text || '';
            fontFamilyControl.value = activeObj.fontFamily.replace(/["']/g, "");
            
            const lhControl = document.getElementById('lineHeightControl');
            if(lhControl) { lhControl.value = (activeObj.lineHeight || 1.16) * 10; document.getElementById('lineHeightVal').textContent = (lhControl.value / 10).toFixed(1); }
            const csControl = document.getElementById('charSpacingControl');
            if(csControl) { csControl.value = activeObj.charSpacing || 0; document.getElementById('charSpacingVal').textContent = csControl.value; }
            
            let fillHex = '#000000';
            if (activeObj.fill) {
                fillHex = new fabric.Color(activeObj.fill).toHex();
                fillHex = '#' + (fillHex === '000000' && activeObj.fill !== 'black' && activeObj.fill !== '#000000' && activeObj.fill !== 'rgb(0,0,0)' ? '000000' : fillHex);
                if(fillHex.length > 7) fillHex = fillHex.substring(0, 7); // Strip alpha if any
            }
            textColorControl.value = fillHex;
            document.getElementById('textColorVal').textContent = fillHex.toUpperCase();
            
            let strokeHex = '#ffffff';
            if (activeObj.stroke) strokeHex = '#' + new fabric.Color(activeObj.stroke).toHex();
            textStrokeColor.value = strokeHex.substring(0, 7);
            
            textStrokeWidth.value = activeObj.strokeWidth || 0;
            textShadowToggle.checked = !!activeObj.shadow;
        } else if(activeObj.type === 'image' && activeObj.customType === 'custom-image') {
            imageControls.classList.remove('hidden'); imageControls.classList.add('flex');
        } else if((activeObj.type === 'group' || activeObj.type === 'path') && activeObj.customType === 'custom-svg') {
            svgControls.classList.remove('hidden'); svgControls.classList.add('flex');
            let tc = activeObj.type === 'group' && activeObj._objects && activeObj._objects.length > 0 ? (activeObj._objects[0].fill || '#000000') : (activeObj.fill || '#000000');
            if (tc) {
                let svgHex = '#' + new fabric.Color(tc).toHex();
                svgHex = svgHex.substring(0, 7);
                svgColorControl.value = svgHex;
                document.getElementById('svgColorVal').textContent = svgHex.toUpperCase();
            }
        }
        
        if(activeObj) {
            const opControl = document.getElementById('imageOpacityControl');
            if(opControl) { opControl.value = Math.round((activeObj.opacity !== undefined ? activeObj.opacity : 1) * 100); document.getElementById('imageOpacityVal').textContent = opControl.value; }
        }
    }

    if(textColorControl) textColorControl.addEventListener('input', () => { document.getElementById('textColorVal').textContent = textColorControl.value.toUpperCase(); });
    if(svgColorControl) svgColorControl.addEventListener('input', () => { document.getElementById('svgColorVal').textContent = svgColorControl.value.toUpperCase(); });
    if(removeColorTolerance) removeColorTolerance.addEventListener('input', function() { document.getElementById('tolValue').textContent = this.value; });

    // Auto-detect bg color
    if(detectBgColorBtn) detectBgColorBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o && o.type === 'image' && o.getElement()) {
            try {
                const el = o.getElement(); const tc = document.createElement('canvas'); const ctx = tc.getContext('2d', { willReadFrequently: true });
                tc.width = el.naturalWidth || el.width || 100; tc.height = el.naturalHeight || el.height || 100;
                ctx.drawImage(el, 0, 0, tc.width, tc.height);
                const px = ctx.getImageData(0, 0, 1, 1).data;
                if(px[3] < 10) { Swal.fire({ icon: 'info', title: 'Transparan', text: 'Sudut gambar sudah transparan.', confirmButtonColor: '#3085d6' }); return; }
                removeColorTarget.value = '#' + ((1 << 24) + (px[0] << 16) + (px[1] << 8) + px[2]).toString(16).slice(1);
            } catch(e) { console.warn('CORS protection', e); }
        }
    });

    if(resetBgBtn) resetBgBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'image') { o.filters = o.filters.filter(f => f.type !== 'RemoveColor'); o.applyFilters(); window.activeCanvas.renderAll(); if(typeof window.saveHistory === 'function') window.saveHistory(); } });
    if(removeBgBtn) removeBgBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o && o.type === 'image') {
            o.filters = o.filters.filter(f => f.type !== 'RemoveColor');
            const dist = (parseInt(removeColorTolerance.value) || 15) / 100;
            o.filters.push(new fabric.Image.filters.RemoveColor({ color: removeColorTarget.value, distance: dist }));
            o.applyFilters(); window.activeCanvas.renderAll();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        }
    });

    // SVG color change
    if(svgColorControl) svgColorControl.addEventListener('input', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o && o.customType === 'custom-svg') {
            function applyDeep(obj, col) {
                if(obj._objects && obj._objects.length > 0) {
                    obj._objects.forEach(c => applyDeep(c, col));
                } else {
                    if(obj.fill && obj.fill !== 'none') obj.set('fill', col);
                    if(obj.stroke && obj.stroke !== 'none') obj.set('stroke', col);
                }
            }
            applyDeep(o, this.value);
            window.activeCanvas.renderAll();
        }
    });

    function hideControls() { 
        if(typeof window.renderLayersList === 'function') window.renderLayersList();
        if(editorControls) { editorControls.classList.add('hidden'); editorControls.classList.remove('flex'); } 
        const backdrop = document.getElementById('editorControlsBackdrop');
        if(backdrop) { backdrop.classList.add('hidden'); backdrop.classList.remove('block'); }
        const mobileEditBtn = document.getElementById('mobileEditBtn');
        if(mobileEditBtn) mobileEditBtn.classList.add('hidden');
    }
    if(deleteObjBtn) deleteObjBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o) { window.activeCanvas.remove(o); hideControls(); } });

    // Duplicate
    const duplicateObjBtn = document.getElementById('duplicateObjBtn');
    if(duplicateObjBtn) duplicateObjBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o) {
            o.clone(function(cloned) {
                window.activeCanvas.discardActiveObject();
                cloned.set({
                    left: cloned.left + 10,
                    top: cloned.top + 10,
                    evented: true,
                });
                cloned.isClone = true;
                if (cloned.type === 'activeSelection') {
                    cloned.canvas = window.activeCanvas;
                    cloned.forEachObject(function(obj) { obj.isClone = true; window.activeCanvas.add(obj); });
                    cloned.setCoords();
                } else {
                    window.activeCanvas.add(cloned);
                }
                window.activeCanvas.setActiveObject(cloned);
                window.activeCanvas.requestRenderAll();
            }, ['customType', 'sablonSize']);
        }
    });

    // Alignment
    const alignCenterHBtn = document.getElementById('alignCenterHBtn');
    const alignCenterVBtn = document.getElementById('alignCenterVBtn');
    if(alignCenterHBtn) alignCenterHBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o) {
            let sideName = 'front';
            try { const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]'); if(el && window.Alpine && window.Alpine.$data) sideName = window.Alpine.$data(el).activeSide || 'front'; } catch(e) {}
            const dims = window.printAreaDims[sideName] || window.printAreaDims['front'];
            const w = dims ? dims.width : window.activeCanvas.width;
            o.set({ left: w / 2, originX: 'center' });
            o.setCoords();
            window.activeCanvas.requestRenderAll();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        }
    });
    if(alignCenterVBtn) alignCenterVBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o) {
            let sideName = 'front';
            try { const el = document.getElementById('editor-alpine') || document.querySelector('[x-data]'); if(el && window.Alpine && window.Alpine.$data) sideName = window.Alpine.$data(el).activeSide || 'front'; } catch(e) {}
            const dims = window.printAreaDims[sideName] || window.printAreaDims['front'];
            const h = dims ? dims.height : window.activeCanvas.height;
            o.set({ top: h / 2, originY: 'center' });
            o.setCoords();
            window.activeCanvas.requestRenderAll();
            if(typeof window.saveHistory === 'function') window.saveHistory();
        }
    });
    
    // Group & Ungroup
    const groupBtn = document.getElementById('groupBtn');
    const ungroupBtn = document.getElementById('ungroupBtn');
    
    if (groupBtn) groupBtn.addEventListener('click', function() {
        const activeObj = window.activeCanvas.getActiveObject();
        if (activeObj && activeObj.type === 'activeSelection') {
            activeObj.toGroup();
            window.activeCanvas.requestRenderAll();
            if(typeof window.saveHistory === 'function') window.saveHistory();
            showControls();
        }
    });

    if (ungroupBtn) ungroupBtn.addEventListener('click', function() {
        const activeObj = window.activeCanvas.getActiveObject();
        if (activeObj && activeObj.type === 'group' && activeObj.customType !== 'custom-svg') {
            activeObj.toActiveSelection();
            window.activeCanvas.requestRenderAll();
            if(typeof window.saveHistory === 'function') window.saveHistory();
            showControls();
        }
    });

    // Zoom Controls
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const zoomResetBtn = document.getElementById('zoomResetBtn');

    if(zoomInBtn) zoomInBtn.addEventListener('click', function() {
        if(window.innerWidth < 768) return; // Disable on mobile
        var oldZoom = window._desktopZoom || 1.0;
        var newZoom = Math.min(3.5, oldZoom * 1.2);
        if (newZoom !== oldZoom) {
            var k = newZoom / oldZoom;
            window._mockupPanX = (window._mockupPanX || 0) * k;
            window._mockupPanY = (window._mockupPanY || 0) * k;
            window._desktopZoom = newZoom;
            if(typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
            if(zoomResetBtn) zoomResetBtn.innerText = Math.round(window._desktopZoom * 100) + '%';
        }
    });

    if(zoomOutBtn) zoomOutBtn.addEventListener('click', function() {
        if(window.innerWidth < 768) return;
        var oldZoom = window._desktopZoom || 1.0;
        var newZoom = Math.max(0.4, oldZoom / 1.2);
        if (newZoom !== oldZoom) {
            var k = newZoom / oldZoom;
            window._mockupPanX = (window._mockupPanX || 0) * k;
            window._mockupPanY = (window._mockupPanY || 0) * k;
            window._desktopZoom = newZoom;
            if(typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
            if(zoomResetBtn) zoomResetBtn.innerText = Math.round(window._desktopZoom * 100) + '%';
        }
    });

    if(zoomResetBtn) zoomResetBtn.addEventListener('click', function() {
        if(window.innerWidth < 768) return;
        window._desktopZoom = 1.0;
        window._mockupPanX = 0;
        window._mockupPanY = 0;
        if(typeof window.setupMobileCanvasScaler === 'function') window.setupMobileCanvasScaler();
        zoomResetBtn.innerText = '100%';
    });

    // Text Properties
    const textAlignLeft = document.getElementById('textAlignLeft');
    const textAlignCenter = document.getElementById('textAlignCenter');
    const textAlignRight = document.getElementById('textAlignRight');
    const lineHeightControl = document.getElementById('lineHeightControl');
    const charSpacingControl = document.getElementById('charSpacingControl');
    
    if(textAlignLeft) textAlignLeft.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('textAlign', 'left'); window.activeCanvas.requestRenderAll(); } });
    if(textAlignCenter) textAlignCenter.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('textAlign', 'center'); window.activeCanvas.requestRenderAll(); } });
    if(textAlignRight) textAlignRight.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'i-text') { o.set('textAlign', 'right'); window.activeCanvas.requestRenderAll(); } });
    
    if(lineHeightControl) lineHeightControl.addEventListener('input', function() {
        document.getElementById('lineHeightVal').textContent = (this.value / 10).toFixed(1);
        const o = window.activeCanvas.getActiveObject(); 
        if(o && o.type === 'i-text') { o.set('lineHeight', this.value / 10); window.activeCanvas.requestRenderAll(); } 
    });
    
    if(charSpacingControl) charSpacingControl.addEventListener('input', function() {
        document.getElementById('charSpacingVal').textContent = this.value;
        const o = window.activeCanvas.getActiveObject(); 
        if(o && o.type === 'i-text') { o.set('charSpacing', parseInt(this.value)); window.activeCanvas.requestRenderAll(); } 
    });

    // Image Properties (Opacity, Flip)
    const imageOpacityControl = document.getElementById('imageOpacityControl');
    const flipHBtn = document.getElementById('flipHBtn');
    const flipVBtn = document.getElementById('flipVBtn');

    if(imageOpacityControl) imageOpacityControl.addEventListener('input', function() {
        document.getElementById('imageOpacityVal').textContent = this.value;
        const o = window.activeCanvas.getActiveObject();
        if(o) { o.set('opacity', this.value / 100); window.activeCanvas.requestRenderAll(); }
    });

    if(flipHBtn) flipHBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o) { o.set('flipX', !o.flipX); window.activeCanvas.requestRenderAll(); if(typeof window.saveHistory === 'function') window.saveHistory(); }
    });
    if(flipVBtn) flipVBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o) { o.set('flipY', !o.flipY); window.activeCanvas.requestRenderAll(); if(typeof window.saveHistory === 'function') window.saveHistory(); }
    });

    const textValueControl = document.getElementById('textValueControl');
    if(textValueControl) {
        textValueControl.addEventListener('input', function() {
            const o = window.activeCanvas.getActiveObject();
            if(o && o.type === 'i-text') {
                o.set('text', this.value);
                window.activeCanvas.renderAll();
            }
        });
    }

    // --- SUBMIT SAVE TO SERVER ---
    const saveBtn = document.getElementById('saveDesignBtn');
    if(saveBtn) saveBtn.addEventListener('click', function() {
        canvasFront.discardActiveObject(); canvasFront.renderAll();
        canvasBack.discardActiveObject(); canvasBack.renderAll();
        if(canvasLeft) { canvasLeft.discardActiveObject(); canvasLeft.renderAll(); }
        if(canvasRight) { canvasRight.discardActiveObject(); canvasRight.renderAll(); }

        let totalObjects = canvasFront.getObjects().length + canvasBack.getObjects().length;
        if(canvasLeft) totalObjects += canvasLeft.getObjects().length;
        if(canvasRight) totalObjects += canvasRight.getObjects().length;

        if(totalObjects === 0) { Swal.fire({ icon: 'warning', title: 'Kanvas Kosong!', text: 'Silakan tambahkan objek desain terlebih dahulu.', confirmButtonColor: '#ef4444' }); return; }

        const activeBaseColor = window.activeBaseColorLocal || '#ffffff';
        


        let frontDataURL = canvasFront.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        let backDataURL = '', leftDataURL = '', rightDataURL = '';
        if(canvasBack && canvasBack.getObjects().length > 0) backDataURL = canvasBack.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        if(canvasLeft && canvasLeft.getObjects().length > 0) leftDataURL = canvasLeft.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        if(canvasRight && canvasRight.getObjects().length > 0) rightDataURL = canvasRight.toDataURL({ format: 'png', quality: 1, multiplier: 4 });

        let rawAssets = [];
        [canvasFront, canvasBack, canvasLeft, canvasRight].forEach(canvas => {
            if(canvas) canvas.getObjects().forEach(obj => { if(obj.customType === 'custom-image' && obj.getSrc) rawAssets.push(obj.getSrc()); });
        });

        const rootData = window.Alpine ? window.Alpine.$data(document.getElementById('editor-alpine') || document.querySelector('[x-data]')) : {};
        const payload = {
            _token: '{{ csrf_token() }}',
            id_produk: '{{ $produk->id_produk }}',
            file_desain: frontDataURL,
            file_desain_belakang: backDataURL,
            file_desain_kiri: leftDataURL,
            file_desain_kanan: rightDataURL,
            canvas_front: JSON.stringify(canvasFront.toJSON(['customType', 'sablonSize'])),
            canvas_back: canvasBack && canvasBack.getObjects().length > 0 ? JSON.stringify(canvasBack.toJSON(['customType', 'sablonSize'])) : null,
            canvas_left: canvasLeft && canvasLeft.getObjects().length > 0 ? JSON.stringify(canvasLeft.toJSON(['customType', 'sablonSize'])) : null,
            canvas_right: canvasRight && canvasRight.getObjects().length > 0 ? JSON.stringify(canvasRight.toJSON(['customType', 'sablonSize'])) : null,
            warna_baju: activeBaseColor,
            raw_assets: rawAssets,
            harga_desain: window.currentHargaDesain || 0,
            detail_sablon: window.currentDetailSablon || ''
        };

        const oldText = this.innerHTML;
        this.innerHTML = 'Memproses... ⏳'; this.disabled = true;

        let submitUrl = '/customer/design';
        let httpMethod = 'POST';
        @if($desainRevisi)
            submitUrl = '/customer/design/{{ $desainRevisi->id_desain }}';
            payload._method = 'PATCH';
        @endif

        fetch(submitUrl, { method: httpMethod, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) })
        .then(res => res.json())
        .then(data => {
            if(data.success) { window.location.href = data.redirect_url; }
            else { Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal menyimpan desain!' }); }
        })
        .catch(error => { console.error('Error:', error); Swal.fire({ icon: 'error', title: 'Kesalahan Koneksi', text: 'Terjadi kesalahan saat menghubungi server.' }); })
        .finally(() => { this.innerHTML = oldText; this.disabled = false; });
    });

    window.recalculateTotalPrice();

  } catch(err) {
    console.error("FATAL ERROR IN EDITOR JS:", err);
    alert("Error in JS: " + err.message);
  }
}

if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initFabricEditor); }
else { initFabricEditor(); }
</script>
