/**
 * ShopMe E-Commerce Client Scripts
 */

const ShopMeOAuth = {
    clientId: 'shopme_web_client',
    clientSecret: 'shopme_oauth_secret_8899',
    authorizeUrl: '/auth/oauth/login'
};

const ShopMeUtils = {
    deepMerge: function(target, source) {
        for (let key in source) {
            if (source.hasOwnProperty(key)) {
                if (typeof source[key] === 'object' && source[key] !== null) {
                    if (!target[key]) {
                        target[key] = Array.isArray(source[key]) ? [] : {};
                    }
                    ShopMeUtils.deepMerge(target[key], source[key]);
                } else {
                    target[key] = source[key];
                }
            } else {
                target[key] = source[key];
            }
        }
        return target;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // DOM-based XSS Check
    const urlParams = new URLSearchParams(window.location.search);
    const promoCode = urlParams.get('promo') || urlParams.get('ref');
    const hash = window.location.hash;

    const bannerContainer = document.getElementById('promo-banner-container');

    if (bannerContainer) {
        if (promoCode) {
            bannerContainer.innerHTML = `
                <div class="promo-banner">
                    🎉 Código Promocional Aplicado: <strong>${promoCode}</strong> (Desconto automático no checkout!)
                </div>
            `;
        } else if (hash.startsWith('#ref=')) {
            const refSource = decodeURIComponent(hash.substring(5));
            bannerContainer.innerHTML = `
                <div class="promo-banner">
                    👥 Recomendado por: <span>${refSource}</span>
                </div>
            `;
        }
    }
});

function checkExternalPrice(productId) {
    const urlInput = document.getElementById(`external-url-${productId}`);
    if (!urlInput || !urlInput.value) {
        alert('Por favor insira um URL para comparar o preço.');
        return;
    }

    const targetUrl = urlInput.value;
    const resultDiv = document.getElementById(`external-price-result-${productId}`);
    if (resultDiv) {
        resultDiv.innerHTML = '<span class="badge badge-info">A consultar fornecedor externo...</span>';
    }

    fetch(`/api/v1/external/check-price?url=${encodeURIComponent(targetUrl)}`)
        .then(res => res.json())
        .then(data => {
            if (resultDiv) {
                if (data.status === 'success') {
                    resultDiv.innerHTML = `<div style="background:#000; color:#0f0; padding:10px; font-family:monospace; border-radius:4px; margin-top:5px; max-height:150px; overflow:auto;"><pre>${escapeHtml(data.response)}</pre></div>`;
                } else {
                    resultDiv.innerHTML = `<span class="badge badge-danger">Erro: ${data.error}</span>`;
                }
            }
        })
        .catch(err => {
            if (resultDiv) {
                resultDiv.innerHTML = `<span class="badge badge-danger">Erro na requisição: ${err.message}</span>`;
            }
        });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
