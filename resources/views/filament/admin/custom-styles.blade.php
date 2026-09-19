<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ==========================================================================
   MTQ KABUPATEN TRENGGALEK 2026 - ADMIN LOGIN DESIGN SYSTEM
   Modern, Dignified, Islamic-Architectural Aesthetics (Navy & Gold)
   ========================================================================== */

:root {
    --mtq-navy-950: #030a14;
    --mtq-navy-900: #061527;
    --mtq-navy-800: #0a2038;
    --mtq-navy-700: #0f2c4c;
    --mtq-navy-600: #183e66;
    --mtq-gold-500: #d4af37;
    --mtq-gold-400: #e5c158;
    --mtq-gold-300: #ffe28a;
    --mtq-gold-600: #b8923e;
    --mtq-emerald: #059669;
    --mtq-surface: #ffffff;
    --mtq-border: rgba(212, 175, 55, 0.35);
}

/* ==========================================================================
   LOGIN PAGE EXPERIENCE (.fi-simple-layout)
   ========================================================================== */
.fi-panel-admin .fi-simple-layout {
    background: radial-gradient(circle at 50% 15%, #0e2945 0%, #071526 50%, #030a14 100%) !important;
    position: relative;
    min-height: 100vh;
    overflow-x: hidden;
    font-family: 'Montserrat', sans-serif !important;
}

/* Authentic subtle Islamic geometric lattice pattern watermark */
.fi-panel-admin .fi-simple-layout::before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: 
        radial-gradient(rgba(212, 175, 55, 0.12) 1.5px, transparent 1.5px),
        radial-gradient(rgba(212, 175, 55, 0.05) 1.5px, transparent 1.5px);
    background-size: 32px 32px;
    background-position: 0 0, 16px 16px;
    pointer-events: none;
    z-index: 1;
}

/* Soft ambient lighting top spotlight */
.fi-panel-admin .fi-simple-layout::after {
    content: '';
    position: fixed;
    top: -120px;
    left: 50%;
    transform: translateX(-50%);
    width: 600px;
    height: 320px;
    background: radial-gradient(ellipse, rgba(212, 175, 55, 0.22) 0%, rgba(14, 41, 69, 0) 70%);
    pointer-events: none;
    z-index: 1;
}

.fi-panel-admin .fi-simple-main-ctn {
    position: relative;
    z-index: 10;
    padding: 40px 16px 50px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Override inline height on logo container in login */
.fi-panel-admin .fi-simple-header .fi-logo {
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

.fi-panel-admin .fi-simple-header {
    margin-bottom: 24px !important;
    overflow: visible !important;
}

/* Luxury Glassmorphic Center Card */
.fi-panel-admin .fi-simple-main {
    background: linear-gradient(180deg, rgba(13, 34, 58, 0.96) 0%, rgba(7, 20, 36, 0.98) 100%) !important;
    border: 1.5px solid rgba(212, 175, 55, 0.45) !important;
    border-radius: 24px !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 40px rgba(212, 175, 55, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.18) !important;
    backdrop-filter: blur(20px);
    position: relative;
    overflow: visible !important;
    max-width: 490px !important;
    width: 100% !important;
    padding: 38px 38px 42px !important;
}

/* Top gold gradient accent bar */
.fi-panel-admin .fi-simple-main::before {
    content: '';
    position: absolute;
    top: 0;
    left: 12%;
    right: 12%;
    height: 3px;
    background: linear-gradient(90deg, transparent, #ffe082 30%, #d4af37 70%, transparent);
    border-radius: 2px;
}

/* Field labels */
.fi-panel-admin .fi-simple-page .fi-fo-field-wrp-label span {
    color: #e2e8f0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: 1px !important;
    text-transform: uppercase !important;
}

/* Input containers */
.fi-panel-admin .fi-simple-page .fi-input-wrp {
    background: rgba(4, 13, 23, 0.9) !important;
    border: 1.5px solid rgba(212, 175, 55, 0.35) !important;
    border-radius: 12px !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.5) !important;
}

.fi-panel-admin .fi-simple-page .fi-input-wrp:hover {
    border-color: rgba(212, 175, 55, 0.6) !important;
}

.fi-panel-admin .fi-simple-page .fi-input-wrp:focus-within {
    border-color: #d4af37 !important;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25), inset 0 2px 4px rgba(0,0,0,0.5) !important;
}

.fi-panel-admin .fi-simple-page input {
    color: #ffffff !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14.5px !important;
    font-weight: 600 !important;
}

.fi-panel-admin .fi-simple-page input::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Action icons inside input (e.g. password reveal button) */
.fi-panel-admin .fi-simple-page .fi-input-wrp button,
.fi-panel-admin .fi-simple-page .fi-input-wrp svg {
    color: #e5c158 !important;
}

/* Primary Submit Button */
.fi-panel-admin .fi-simple-page .fi-btn-primary,
.fi-panel-admin .fi-simple-page button[type="submit"] {
    background: linear-gradient(135deg, #d4af37 0%, #b8923e 100%) !important;
    color: #071524 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-weight: 800 !important;
    font-size: 13.5px !important;
    letter-spacing: 1.5px !important;
    text-transform: uppercase !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 14px 24px !important;
    box-shadow: 0 4px 18px rgba(212, 175, 55, 0.45), 0 2px 4px rgba(0,0,0,0.3) !important;
    transition: all 0.25s ease !important;
    width: 100% !important;
    cursor: pointer !important;
}

.fi-panel-admin .fi-simple-page .fi-btn-primary:hover,
.fi-panel-admin .fi-simple-page button[type="submit"]:hover {
    background: linear-gradient(135deg, #e5b958 0%, #c8a34d 100%) !important;
    transform: translateY(-1.5px) !important;
    box-shadow: 0 6px 24px rgba(212, 175, 55, 0.6), 0 3px 6px rgba(0,0,0,0.4) !important;
}

/* Checkbox */
.fi-panel-admin .fi-simple-page .fi-checkbox-input {
    border-radius: 5px !important;
    border-color: rgba(212, 175, 55, 0.5) !important;
    background: rgba(4, 13, 23, 0.8) !important;
}

.fi-panel-admin .fi-simple-page .fi-checkbox-input:checked {
    background-color: #d4af37 !important;
    border-color: #d4af37 !important;
}

.fi-panel-admin .fi-simple-page .fi-fo-checkbox span {
    color: #cbd5e1 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}

/* Validation error messages */
.fi-panel-admin .fi-simple-page .fi-fo-field-wrp-error-message {
    color: #f87171 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-weight: 600 !important;
    font-size: 12px !important;
}
</style>
