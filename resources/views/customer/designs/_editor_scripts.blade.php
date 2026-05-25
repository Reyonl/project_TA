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

    window.switchCanvasSide = function(side) {
        if(window.activeCanvas) { window.activeCanvas.discardActiveObject(); window.activeCanvas.renderAll(); }
        if (typeof hideControls === 'function') hideControls();
        if(side === 'front') window.activeCanvas = canvasFront;
        else if(side === 'back') window.activeCanvas = canvasBack;
        else if(side === 'left') window.activeCanvas = canvasLeft;
        else if(side === 'right') window.activeCanvas = canvasRight;
    };

    // Generate preview thumbnails for Step 4
    const mockupFrontUrl = '{{ asset("images/mockups/" . (match($produk->jenis_produk) { "kaos" => "kaos", "hoodie" => "hoodie", "polo" => "polo", "seragam" => "seragam", default => "kaos" }) . ".png") }}';
    const mockupBackUrl = '{{ asset("images/mockups/" . (match($produk->jenis_produk) { "kaos" => "kaos", "hoodie" => "hoodie", "polo" => "polo", "seragam" => "seragam", default => "kaos" }) . "_belakang.png") }}';

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
        } catch(e) { console.warn('Preview generation error:', e); }
    };

    // Watch for step changes to generate previews
    const rootEl = document.querySelector('[x-data]');
    if(rootEl && window.Alpine) {
        Alpine.effect(() => {
            const data = Alpine.$data(rootEl);
            if(data.currentStep === 4) { setTimeout(() => window.generatePreviews(), 300); }
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
    fabric.Object.prototype.cornerSize = 14;
    fabric.Object.prototype.padding = 10;
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
            const text = new fabric.IText('Teks Anda', { left: 20, top: 20, fontFamily: 'Arial', fill: '#000000', fontSize: 40, fontWeight: 'bold', sablonSize: 'a5' });
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
        try { const el = document.querySelector('[x-data]'); if(el && window.Alpine && window.Alpine.$data) sideName = window.Alpine.$data(el).activeSide || 'front'; } catch(e) {}
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
        reader.readAsDataURL(e.target.files[0]); e.target.value = '';
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

    // Perhitungan Harga Sablon Dinamis Per Objek
    window.recalculateTotalPrice = function() {
        let totalDesignPrice = 0;
        let breakdownTextFront = [];
        let breakdownTextBack = [];

        if (canvasFront) {
            canvasFront.getObjects().forEach((obj, idx) => {
                const size = obj.sablonSize || 'a5';
                let price = 10000;
                let sizeLabel = 'A5 Logo';
                if (size === 'a4') { price = 25000; sizeLabel = 'A4'; }
                else if (size === 'a3') { price = 35000; sizeLabel = 'A3'; }
                
                totalDesignPrice += price;
                let typeLabel = obj.type === 'i-text' ? 'Teks' : (obj.customType === 'custom-svg' ? 'Stiker' : 'Gambar');
                breakdownTextFront.push(`${typeLabel} #${idx+1} (${sizeLabel}: Rp ${price.toLocaleString('id-ID')})`);
            });
        }

        if (canvasBack) {
            canvasBack.getObjects().forEach((obj, idx) => {
                const size = obj.sablonSize || 'a5';
                let price = 10000;
                let sizeLabel = 'A5 Logo';
                if (size === 'a4') { price = 25000; sizeLabel = 'A4'; }
                else if (size === 'a3') { price = 35000; sizeLabel = 'A3'; }
                
                totalDesignPrice += price;
                let typeLabel = obj.type === 'i-text' ? 'Teks' : (obj.customType === 'custom-svg' ? 'Stiker' : 'Gambar');
                breakdownTextBack.push(`${typeLabel} #${idx+1} (${sizeLabel}: Rp ${price.toLocaleString('id-ID')})`);
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
            if (breakdownTextFront.length === 0 && breakdownTextBack.length === 0) {
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
        if(breakdownTextBack.length > 0) desc += 'Belakang: ' + breakdownTextBack.join(', ') + '.';
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

    window.setObjectSablonSize = function(size) {
        const activeObj = window.activeCanvas.getActiveObject();
        if (activeObj) {
            activeObj.sablonSize = size;
            window.updateSablonSizeButtons(size);
            window.recalculateTotalPrice();
        }
    };

    // Editor controls logic
    const canvasesToHandle = [canvasFront, canvasBack];
    if(canvasLeft) canvasesToHandle.push(canvasLeft);
    if(canvasRight) canvasesToHandle.push(canvasRight);
    canvasesToHandle.forEach(c => { 
        c.on('selection:created', showControls); 
        c.on('selection:updated', showControls); 
        c.on('selection:cleared', hideControls); 
        c.on('object:added', window.recalculateTotalPrice);
        c.on('object:removed', window.recalculateTotalPrice);
    });

    function showControls(e) {
        if(!editorControls) return;
        editorControls.classList.remove('hidden'); editorControls.classList.add('flex');
        const activeObj = (e && e.selected) ? e.selected[0] : window.activeCanvas.getActiveObject();
        if(!activeObj) return;
        textControls.classList.add('hidden'); textControls.classList.remove('flex');
        imageControls.classList.add('hidden'); imageControls.classList.remove('flex');
        svgControls.classList.add('hidden'); svgControls.classList.remove('flex');

        // Set active size buttons
        const size = activeObj.sablonSize || 'a5';
        window.updateSablonSizeButtons(size);

        if(activeObj.type === 'i-text') {
            textControls.classList.remove('hidden'); textControls.classList.add('flex');
            fontFamilyControl.value = activeObj.fontFamily.replace(/["']/g, "");
            textColorControl.value = activeObj.fill;
            document.getElementById('textColorVal').textContent = activeObj.fill.toUpperCase();
            textStrokeColor.value = activeObj.stroke || '#ffffff';
            textStrokeWidth.value = activeObj.strokeWidth || 0;
            textShadowToggle.checked = !!activeObj.shadow;
        } else if(activeObj.type === 'image' && activeObj.customType === 'custom-image') {
            imageControls.classList.remove('hidden'); imageControls.classList.add('flex');
        } else if((activeObj.type === 'group' || activeObj.type === 'path') && activeObj.customType === 'custom-svg') {
            svgControls.classList.remove('hidden'); svgControls.classList.add('flex');
            let tc = activeObj.type === 'group' && activeObj._objects && activeObj._objects.length > 0 ? (activeObj._objects[0].fill || '#000000') : (activeObj.fill || '#000000');
            if(typeof tc === 'string' && tc.startsWith('#')) { svgColorControl.value = tc; document.getElementById('svgColorVal').textContent = tc.toUpperCase(); }
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

    if(resetBgBtn) resetBgBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o && o.type === 'image') { o.filters = o.filters.filter(f => f.type !== 'RemoveColor'); o.applyFilters(); window.activeCanvas.renderAll(); } });
    if(removeBgBtn) removeBgBtn.addEventListener('click', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o && o.type === 'image') {
            o.filters = o.filters.filter(f => f.type !== 'RemoveColor');
            const dist = (parseInt(removeColorTolerance.value) || 15) / 100;
            o.filters.push(new fabric.Image.filters.RemoveColor({ color: removeColorTarget.value, distance: dist }));
            o.applyFilters(); window.activeCanvas.renderAll();
        }
    });

    // SVG color change
    if(svgColorControl) svgColorControl.addEventListener('input', function() {
        const o = window.activeCanvas.getActiveObject();
        if(o && o.type === 'group' && o.customType === 'custom-svg') {
            function applyDeep(obj, col) { if(obj._objects) obj._objects.forEach(c => applyDeep(c, col)); else { if(obj.fill && obj.fill !== 'none') obj.set('fill', col); if(obj.stroke && obj.stroke !== 'none') obj.set('stroke', col); } }
            applyDeep(o, this.value); window.activeCanvas.renderAll();
        }
    });

    function hideControls() { if(editorControls) { editorControls.classList.add('hidden'); editorControls.classList.remove('flex'); } }
    if(deleteObjBtn) deleteObjBtn.addEventListener('click', function() { const o = window.activeCanvas.getActiveObject(); if(o) { window.activeCanvas.remove(o); hideControls(); } });

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
        
        function getMaxDimensions(canvas) {
            let maxW = 10, maxH = 10;
            if (canvas) {
                canvas.getObjects().forEach(obj => {
                    const size = obj.sablonSize || 'a5';
                    if (size === 'a3') { maxW = 25; maxH = 35; }
                    else if (size === 'a4' && maxW < 20) { maxW = 20; maxH = 25; }
                });
            }
            return { width: maxW, height: maxH };
        }

        const frontDims = getMaxDimensions(canvasFront);
        const backDims = getMaxDimensions(canvasBack);

        let frontDataURL = canvasFront.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        let backDataURL = '', leftDataURL = '', rightDataURL = '';
        if(canvasBack && canvasBack.getObjects().length > 0) backDataURL = canvasBack.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        if(canvasLeft && canvasLeft.getObjects().length > 0) leftDataURL = canvasLeft.toDataURL({ format: 'png', quality: 1, multiplier: 4 });
        if(canvasRight && canvasRight.getObjects().length > 0) rightDataURL = canvasRight.toDataURL({ format: 'png', quality: 1, multiplier: 4 });

        let rawAssets = [];
        [canvasFront, canvasBack, canvasLeft, canvasRight].forEach(canvas => {
            if(canvas) canvas.getObjects().forEach(obj => { if(obj.customType === 'custom-image' && obj.getSrc) rawAssets.push(obj.getSrc()); });
        });

        const rootData = window.Alpine ? window.Alpine.$data(document.querySelector('[x-data]')) : {};
        const payload = {
            _token: '{{ csrf_token() }}',
            id_produk: '{{ $produk->id_produk }}',
            file_desain: frontDataURL,
            file_desain_belakang: backDataURL,
            file_desain_kiri: leftDataURL,
            file_desain_kanan: rightDataURL,
            lebar_cm: frontDims.width,
            tinggi_cm: frontDims.height,
            warna_baju: activeBaseColor,
            raw_assets: rawAssets,
            harga_desain: window.currentHargaDesain || 0,
            detail_sablon: window.currentDetailSablon || ''
        };
        if(backDataURL !== '') { 
            payload.lebar_cm_belakang = backDims.width; 
            payload.tinggi_cm_belakang = backDims.height; 
        }

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
