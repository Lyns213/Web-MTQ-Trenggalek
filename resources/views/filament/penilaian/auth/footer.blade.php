<style>
/* Force bright text on choices select button in login form */
.fi-panel-penilaian .choices,
.fi-panel-penilaian .choices__inner,
.fi-panel-penilaian .choices__list,
.fi-panel-penilaian .choices__item,
.fi-panel-penilaian .choices__heading,
.fi-panel-penilaian .choices__button,
.fi-panel-penilaian .fi-fo-select *,
.fi-panel-penilaian .fi-fo-select button,
.fi-panel-penilaian .fi-fo-select span,
.fi-panel-penilaian .fi-fo-select div {
    background-color: transparent !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    font-weight: 700 !important;
    opacity: 1 !important;
}

.fi-panel-penilaian .choices__placeholder {
    color: rgba(255, 255, 255, 0.7) !important;
    -webkit-text-fill-color: rgba(255, 255, 255, 0.7) !important;
}

.fi-panel-penilaian .choices__list--dropdown,
.fi-panel-penilaian .choices__list[aria-expanded] {
    background-color: #061527 !important;
    border: 1.5px solid rgba(212, 175, 55, 0.5) !important;
    border-radius: 12px !important;
}

.fi-panel-penilaian .choices__list--dropdown .choices__item--selectable.is-highlighted {
    background-color: rgba(212, 175, 55, 0.3) !important;
    color: #ffe28a !important;
    -webkit-text-fill-color: #ffe28a !important;
}

/* Style X clear button */
.fi-panel-penilaian .choices__button,
.fi-panel-penilaian .fi-fo-select button[aria-label*="Remove"],
.fi-panel-penilaian .fi-fo-select [data-button],
.fi-panel-penilaian .fi-fo-select .fi-select-input-clear-btn {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E") !important;
    background-size: 14px 14px !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    opacity: 0.95 !important;
    width: 22px !important;
    height: 22px !important;
    border-radius: 4px !important;
    filter: none !important;
}

.fi-panel-penilaian .choices__button:hover,
.fi-panel-penilaian .fi-fo-select button[aria-label*="Remove"]:hover {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23ffe28a' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E") !important;
    opacity: 1 !important;
}
</style>

<div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(212, 175, 55, 0.2); text-align: center;">
    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 6px;">
        <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase;">
            Pemerintah Kabupaten Trenggalek
        </span>
        <span style="color: #64748b;">&bull;</span>
        <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #e5b958; letter-spacing: 0.5px; text-transform: uppercase;">
            LPTQ
        </span>
    </div>
    <div style="font-family: 'Montserrat', sans-serif; font-size: 10.5px; color: #64748b;">
        Sistem Penilaian Resmi Musabaqah Tilawatil Qur'an Tahun 2026
    </div>
</div>
