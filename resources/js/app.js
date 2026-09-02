import './bootstrap';

document.querySelectorAll('[data-review-form]').forEach((form) => {
    const confirmation = form.querySelector('[data-review-confirmation]');
    const submit = form.querySelector('[data-review-submit]');

    if (!confirmation || !submit) return;

    const syncReviewSubmit = () => {
        submit.disabled = !confirmation.checked;
        submit.setAttribute('aria-disabled', String(!confirmation.checked));
    };

    confirmation.addEventListener('change', syncReviewSubmit);
    syncReviewSubmit();
});

const achievementCard = document.querySelector('[data-achievement-card]');
if (achievementCard) {
    const canvas = document.querySelector('[data-achievement-canvas]');
    const feedback = document.querySelector('[data-share-feedback]');
    const ctx = canvas.getContext('2d');
    const data = achievementCard.dataset;

    const wrapText = (text, x, y, maxWidth, lineHeight, maxLines = 3) => {
        const words = text.split(' '); let line = ''; let lines = 0;
        for (const word of words) {
            const test = `${line}${word} `;
            if (ctx.measureText(test).width > maxWidth && line) {
                ctx.fillText(line.trim(), x, y); line = `${word} `; y += lineHeight; lines++;
                if (lines >= maxLines - 1) break;
            } else line = test;
        }
        ctx.fillText(line.trim(), x, y);
    };

    const renderPng = async () => {
        await document.fonts.ready;
        const gradient = ctx.createLinearGradient(0, 0, 1080, 1350); gradient.addColorStop(0, '#102d38'); gradient.addColorStop(1, '#075b5e');
        ctx.fillStyle = gradient; ctx.fillRect(0, 0, 1080, 1350);
        ctx.strokeStyle = 'rgba(33,184,166,.25)'; ctx.lineWidth = 3;
        for (let radius = 170; radius < 700; radius += 70) { ctx.beginPath(); ctx.arc(1030, 80, radius, 0, Math.PI * 2); ctx.stroke(); }
        ctx.fillStyle = '#21b8a6'; ctx.font = '700 28px Inter, sans-serif'; ctx.fillText('TURISMOSV  ·  PASAPORTE DIGITAL', 78, 105);
        ctx.fillStyle = '#f3b63f'; ctx.beginPath(); ctx.arc(540, 385, 145, 0, Math.PI * 2); ctx.fill();
        ctx.fillStyle = '#102d38'; ctx.textAlign = 'center'; ctx.font = '700 112px Sora, sans-serif'; ctx.fillText('✦', 540, 420);
        ctx.fillStyle = '#21b8a6'; ctx.font = '700 24px Inter, sans-serif'; ctx.fillText('LOGRO DESBLOQUEADO', 540, 600);
        ctx.fillStyle = '#ffffff'; ctx.font = '700 72px Sora, sans-serif'; wrapText(data.achievement, 540, 700, 850, 82, 2);
        ctx.fillStyle = 'rgba(255,255,255,.72)'; ctx.font = '400 30px Inter, sans-serif'; wrapText(data.description, 540, 880, 760, 43, 3);
        ctx.textAlign = 'left'; ctx.strokeStyle = 'rgba(255,255,255,.2)'; ctx.beginPath(); ctx.moveTo(78, 1060); ctx.lineTo(1002, 1060); ctx.stroke();
        ctx.fillStyle = 'rgba(255,255,255,.55)'; ctx.font = '700 18px Inter, sans-serif'; ctx.fillText('VIAJERO', 78, 1115); ctx.fillText('FECHA', 630, 1115);
        ctx.fillStyle = '#ffffff'; ctx.font = '700 29px Sora, sans-serif'; ctx.fillText(data.name, 78, 1160); ctx.fillText(data.date, 630, 1160);
        ctx.fillStyle = '#f3b63f'; ctx.font = '700 30px Sora, sans-serif'; ctx.fillText(`+${data.points} PTS`, 78, 1260);
        return new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
    };

    const filename = `turismosv-${data.achievement.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-')}.png`;
    const download = async () => { const blob = await renderPng(); const url = URL.createObjectURL(blob); const link = Object.assign(document.createElement('a'), {href:url, download:filename}); link.click(); URL.revokeObjectURL(url); feedback.textContent = 'Tarjeta descargada. Ya puedes publicarla en tu red favorita.'; };
    document.querySelector('[data-achievement-download]')?.addEventListener('click', download);
    document.querySelector('[data-achievement-share]')?.addEventListener('click', async () => {
        const blob = await renderPng(); const file = new File([blob], filename, {type:'image/png'});
        if (navigator.canShare?.({files:[file]})) { try { await navigator.share({title:`Mi logro en TurismoSV: ${data.achievement}`,text:'Estoy recorriendo El Salvador con TurismoSV.',files:[file]}); feedback.textContent='Tarjeta compartida desde tu dispositivo.'; } catch (error) { if (error.name !== 'AbortError') feedback.textContent='No fue posible compartirla. Puedes descargar el PNG.'; } }
        else await download();
    });
}
