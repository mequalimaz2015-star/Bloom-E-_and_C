<div class="card">
    <div class="card-header"><span class="card-title">Live Communication Hub</span></div>
    <p style="color: #64748b; margin-bottom: 30px;">Manage your WhatsApp and Telegram orders directly from this
        dashboard. Click below to launch the integrated chat windows.</p>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- WhatsApp Hub -->
        <div class="stat-card"
            style="background: #e7f9ed; border: 1px solid #c6f1d6; padding: 40px; text-align: center; cursor: pointer; transition: 0.3s;"
            onclick="openChat('https://web.whatsapp.com/')">
            <div style="font-size: 50px; color: #25d366; margin-bottom: 20px;"><i class="fa-brands fa-whatsapp"></i>
            </div>
            <h3 style="color: #1a1a1a; margin-bottom: 10px;">WhatsApp Web</h3>
            <p style="font-size: 14px; color: #666;">Open WhatsApp in an integrated popup window to respond to customer
                inquiries.</p>
            <button class="btn btn-primary" style="margin-top: 20px; background: #25d366; border: none;">Launch
                WhatsApp</button>
        </div>

        <!-- Telegram Hub -->
        <div class="stat-card"
            style="background: #e3f2fd; border: 1px solid #bbdefb; padding: 40px; text-align: center; cursor: pointer; transition: 0.3s;"
            onclick="openChat('https://web.telegram.org/a/')">
            <div style="font-size: 50px; color: #0088cc; margin-bottom: 20px;"><i class="fa-brands fa-telegram"></i>
            </div>
            <h3 style="color: #1a1a1a; margin-bottom: 10px;">Telegram Web</h3>
            <p style="font-size: 14px; color: #666;">Access your Telegram messages directly. Stay connected without
                leaving the dashboard.</p>
            <button class="btn btn-primary" style="margin-top: 20px; background: #0088cc; border: none;">Launch
                Telegram</button>
        </div>
    </div>

    <div
        style="margin-top: 40px; padding: 20px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; display: flex; gap: 15px; align-items: center;">
        <i class="fa-solid fa-circle-info" style="color: #d97706; font-size: 20px;"></i>
        <p style="font-size: 13px; color: #92400e; margin: 0;"><strong>Pro Tip:</strong> These platforms block being
            embedded directly inside the page for security. These buttons open optimized **floating windows** that you
            can keep open alongside your dashboard!</p>
    </div>
</div>

<script>
    function openChat(url) {
        const width = 1100;
        const height = 800;
        const left = (window.screen.width / 2) - (width / 2);
        const top = (window.screen.height / 2) - (height / 2);

        window.open(url, 'ChatWindow', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes`);
    }
</script>