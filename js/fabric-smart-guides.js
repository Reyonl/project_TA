/**
 * Fabric.js Smart Guidelines
 * Adds snapping alignment lines when objects are moved.
 */
function initAligningGuidelines(canvas) {
    var ctx = canvas.getSelectionContext(),
        aligningLineOffset = 3,
        aligningLineMargin = 4,
        aligningLineWidth = 1,
        aligningLineColor = 'rgb(255, 0, 255)', // Magenta color like Canva
        viewportTransform,
        zoom = 1;

    function drawVerticalLine(coords) {
        drawLine(
            coords.x + 0.5,
            coords.y1 > coords.y2 ? coords.y2 : coords.y1,
            coords.x + 0.5,
            coords.y2 > coords.y1 ? coords.y2 : coords.y1);
    }

    function drawHorizontalLine(coords) {
        drawLine(
            coords.x1 > coords.x2 ? coords.x2 : coords.x1,
            coords.y + 0.5,
            coords.x2 > coords.x1 ? coords.x2 : coords.x1,
            coords.y + 0.5);
    }

    function drawLine(x1, y1, x2, y2) {
        if(!ctx) return;
        ctx.save();
        ctx.lineWidth = aligningLineWidth;
        ctx.strokeStyle = aligningLineColor;
        ctx.setLineDash([4, 4]); // Dashed line
        ctx.beginPath();
        ctx.moveTo(x1 * zoom + (viewportTransform[4] || 0), y1 * zoom + (viewportTransform[5] || 0));
        ctx.lineTo(x2 * zoom + (viewportTransform[4] || 0), y2 * zoom + (viewportTransform[5] || 0));
        ctx.stroke();
        ctx.restore();
    }

    function isInRange(value1, value2) {
        value1 = Math.round(value1);
        value2 = Math.round(value2);
        return Math.abs(value1 - value2) <= aligningLineOffset;
    }

    var verticalLines = [],
        horizontalLines = [];

    canvas.on('mouse:down', function () {
        viewportTransform = canvas.viewportTransform || [1,0,0,1,0,0];
        zoom = canvas.getZoom();
    });

    canvas.on('object:moving', function (e) {
        var activeObject = e.target,
            canvasObjects = canvas.getObjects(),
            activeObjectCenter = activeObject.getCenterPoint(),
            activeObjectBoundingRect = activeObject.getBoundingRect(),
            activeObjectHeight = activeObjectBoundingRect.height / (viewportTransform[3] || 1),
            activeObjectWidth = activeObjectBoundingRect.width / (viewportTransform[0] || 1),
            horizontalInTheRange = false,
            verticalInTheRange = false,
            transform = canvas._currentTransform;

        if (!transform) return;

        verticalLines = [];
        horizontalLines = [];

        // 1. Check Canvas Center
        var canvasCenter = { x: canvas.width / 2, y: canvas.height / 2 };
        
        // Vertical Center Canvas
        if (isInRange(activeObjectCenter.x, canvasCenter.x)) {
            verticalInTheRange = true;
            verticalLines.push({
                x: canvasCenter.x,
                y1: 0,
                y2: canvas.height
            });
            activeObject.setPositionByOrigin(new fabric.Point(canvasCenter.x, activeObjectCenter.y), 'center', 'center');
            activeObjectBoundingRect = activeObject.getBoundingRect();
            activeObjectCenter = activeObject.getCenterPoint();
        }

        // Horizontal Center Canvas
        if (isInRange(activeObjectCenter.y, canvasCenter.y)) {
            horizontalInTheRange = true;
            horizontalLines.push({
                y: canvasCenter.y,
                x1: 0,
                x2: canvas.width
            });
            activeObject.setPositionByOrigin(new fabric.Point(activeObjectCenter.x, canvasCenter.y), 'center', 'center');
            activeObjectBoundingRect = activeObject.getBoundingRect();
            activeObjectCenter = activeObject.getCenterPoint();
        }

        // 2. Check Other Objects
        for (var i = canvasObjects.length; i--;) {
            if (canvasObjects[i] === activeObject) continue;
            if (canvasObjects[i].excludeFromExport) continue; // Ignore helpers

            var objectBoundingRect = canvasObjects[i].getBoundingRect(),
                objectHeight = objectBoundingRect.height / (viewportTransform[3] || 1),
                objectWidth = objectBoundingRect.width / (viewportTransform[0] || 1),
                objectCenter = canvasObjects[i].getCenterPoint();

            var objectLeft = objectBoundingRect.left;
            var objectRight = objectBoundingRect.left + objectBoundingRect.width;
            var objectTop = objectBoundingRect.top;
            var objectBottom = objectBoundingRect.top + objectBoundingRect.height;

            var activeLeft = activeObjectBoundingRect.left;
            var activeRight = activeObjectBoundingRect.left + activeObjectBoundingRect.width;
            var activeTop = activeObjectBoundingRect.top;
            var activeBottom = activeObjectBoundingRect.top + activeObjectBoundingRect.height;

            // snap by the vertical center line
            if (isInRange(objectCenter.x, activeObjectCenter.x)) {
                verticalInTheRange = true;
                verticalLines.push({
                    x: objectCenter.x,
                    y1: Math.min(objectCenter.y, activeObjectCenter.y) - objectHeight,
                    y2: Math.max(objectCenter.y, activeObjectCenter.y) + objectHeight
                });
                activeObject.setPositionByOrigin(new fabric.Point(objectCenter.x, activeObjectCenter.y), 'center', 'center');
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }

            // snap by the horizontal center line
            if (isInRange(objectCenter.y, activeObjectCenter.y)) {
                horizontalInTheRange = true;
                horizontalLines.push({
                    y: objectCenter.y,
                    x1: Math.min(objectCenter.x, activeObjectCenter.x) - objectWidth,
                    x2: Math.max(objectCenter.x, activeObjectCenter.x) + objectWidth
                });
                activeObject.setPositionByOrigin(new fabric.Point(activeObjectCenter.x, objectCenter.y), 'center', 'center');
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }
            
            // snap by the left edge
            if (isInRange(objectLeft, activeLeft)) {
                verticalInTheRange = true;
                verticalLines.push({
                    x: objectLeft,
                    y1: Math.min(objectTop, activeTop) - Math.max(objectHeight, activeObjectHeight),
                    y2: Math.max(objectTop, activeTop) + Math.max(objectHeight, activeObjectHeight)
                });
                activeObject.set({ left: activeObject.left + (objectLeft - activeLeft) });
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }

            // snap by the right edge
            if (isInRange(objectRight, activeRight)) {
                verticalInTheRange = true;
                verticalLines.push({
                    x: objectRight,
                    y1: Math.min(objectTop, activeTop) - Math.max(objectHeight, activeObjectHeight),
                    y2: Math.max(objectTop, activeTop) + Math.max(objectHeight, activeObjectHeight)
                });
                activeObject.set({ left: activeObject.left + (objectRight - activeRight) });
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }

            // snap by the top edge
            if (isInRange(objectTop, activeTop)) {
                horizontalInTheRange = true;
                horizontalLines.push({
                    y: objectTop,
                    x1: Math.min(objectLeft, activeLeft) - Math.max(objectWidth, activeObjectWidth),
                    x2: Math.max(objectLeft, activeLeft) + Math.max(objectWidth, activeObjectWidth)
                });
                activeObject.set({ top: activeObject.top + (objectTop - activeTop) });
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }

            // snap by the bottom edge
            if (isInRange(objectBottom, activeBottom)) {
                horizontalInTheRange = true;
                horizontalLines.push({
                    y: objectBottom,
                    x1: Math.min(objectLeft, activeLeft) - Math.max(objectWidth, activeObjectWidth),
                    x2: Math.max(objectLeft, activeLeft) + Math.max(objectWidth, activeObjectWidth)
                });
                activeObject.set({ top: activeObject.top + (objectBottom - activeBottom) });
                activeObjectBoundingRect = activeObject.getBoundingRect();
                activeObjectCenter = activeObject.getCenterPoint();
            }
        }

        if (!horizontalInTheRange) {
            horizontalLines.length = 0;
        }

        if (!verticalInTheRange) {
            verticalLines.length = 0;
        }
    });

    canvas.on('before:render', function () {
        if(canvas.contextTop && ctx) {
            canvas.clearContext(canvas.contextTop);
        }
    });

    canvas.on('after:render', function () {
        if(!ctx) return;
        for (var i = verticalLines.length; i--;) {
            drawVerticalLine(verticalLines[i]);
        }
        for (var j = horizontalLines.length; j--;) {
            drawHorizontalLine(horizontalLines[j]);
        }
        verticalLines.length = horizontalLines.length = 0;
    });

    canvas.on('mouse:up', function () {
        verticalLines.length = horizontalLines.length = 0;
        canvas.renderAll();
    });
}
