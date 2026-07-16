<style>
    /* ===== BASE STYLES ===== */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    [x-cloak] { display: none !important; }

    /* Bottom sheet drag handle indicator */
    .sheet-handle {
        width: 36px;
        height: 4px;
        background: #cbd5e1;
        border-radius: 2px;
        margin: 10px auto 4px;
        flex-shrink: 0;
    }

    /* Backdrop overlay (shared by sidebar & properties sheets) */
    .mobile-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 45;
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(4px);
    }

    /* ===== MOBILE RESPONSIVE OVERRIDES (< md / 768px) ===== */
    @media (max-width: 767px) {

        /* Use dynamic viewport height on mobile to avoid browser chrome issues */
        .editor-main-container {
            height: calc(100dvh - 140px) !important;
        }

        /* Canvas scaling wrapper — JS sets width/height/transform dynamically */
        .mobile-canvas-scaler {
            overflow: visible;
            position: relative;
            margin: 0 auto;
        }
        .mobile-canvas-scaler #mockupContainer {
            transform-origin: top left;
        }

        /* ---- Sidebar → Slide-Up Bottom Sheet ---- */
        .editor-sidebar {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            top: auto !important;
            width: 100% !important;
            max-height: 55vh;
            border-radius: 1.25rem 1.25rem 0 0;
            border-right: none !important;
            border-top: 1px solid #e2e8f0;
            z-index: 50 !important;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.12);
        }
        .editor-sidebar .custom-scrollbar {
            max-height: 42vh;
        }

        /* ---- Properties Panel → Slide-Up Bottom Sheet ---- */
        .editor-properties {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            top: auto !important;
            width: 100% !important;
            max-height: 60vh;
            border-radius: 1.25rem 1.25rem 0 0;
            border-left: none !important;
            border-top: 1px solid #e2e8f0;
            z-index: 50 !important;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.12);
        }
        /* Compact padding inside properties bottom sheet */
        .editor-properties .p-6 {
            padding: 1rem 1.25rem;
        }
        .editor-properties .space-y-8 > * + * {
            margin-top: 1.25rem;
        }
        .editor-properties .pt-6 {
            padding-top: 1rem;
        }
        .editor-properties .pt-8 {
            padding-top: 1.25rem;
        }

        /* Fabric.js canvas touch optimization */
        .canvas-container {
            touch-action: none;
        }

        /* Size buttons — tighter on mobile */
        #objectSablonSizeControl .grid {
            gap: 0.375rem;
        }
        #objectSablonSizeControl .size-btn {
            padding: 0.375rem;
        }
    }
</style>
