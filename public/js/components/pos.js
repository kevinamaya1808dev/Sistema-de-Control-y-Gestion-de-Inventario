document.addEventListener('alpine:init', () => {
    Alpine.data('posSystem', (productsData = [], imageRouteUrl = '') => ({
        products: productsData,
        imageRouteUrl: imageRouteUrl,
        searchQuery: '',
        cart: [],

        modals: {
            selectSize: false,
            checkoutModal: false,
            ticketModal: false
        },

        selectedProductForSize: null,
        selectedSize: null,

        paymentMethod: 'cash', // 'cash', 'card', 'transfer'
        amountReceived: '',
        change: 0,
        reference: '', // folio/autorización para tarjeta o transferencia

        lastTicket: {
            items: [],
            total: 0,
            paymentMethod: 'cash',
            amountReceived: 0,
            amountPaid: 0,
            change: 0,
            reference: ''
        },

        swalConfig: {
            background: '#0f172a',
            color: '#f8fafc',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
        },

        get filteredProducts() {
            if (!this.searchQuery.trim()) return this.products;
            const q = this.searchQuery.toLowerCase().trim();
            return this.products.filter(p => {
                const nameMatch = p.name && p.name.toLowerCase().includes(q);
                const codeMatch = p.barcode && p.barcode.toString().toLowerCase().includes(q);
                const skuMatch = p.sku && p.sku.toString().toLowerCase().includes(q);
                return nameMatch || codeMatch || skuMatch;
            });
        },

        get total() {
            return this.cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.qty), 0);
        },

        // Referencia obligatoria solo cuando el pago no es en efectivo
        get referenceRequired() {
            return this.paymentMethod === 'card' || this.paymentMethod === 'transfer';
        },

        showAlert(icon, title, text, extraOpts = {}) {
            return Swal.fire({ ...this.swalConfig, icon, title, text, ...extraOpts });
        },

        openModal(name) {
            if (this.modals.hasOwnProperty(name)) {
                this.modals[name] = true;
                if (name === 'checkoutModal') {
                    this.amountReceived = '';
                    this.change = 0;
                    this.reference = '';
                    this.paymentMethod = 'cash'; // Por defecto abre en efectivo
                }
            }
        },

        closeModal(name) {
            if (this.modals.hasOwnProperty(name)) {
                this.modals[name] = false;
            }
        },

        calculateChange() {
            const received = parseFloat(this.amountReceived) || 0;
            this.change = Math.max(0, received - this.total);
        },

        getImageUrl(imagePath) {
            if (!imagePath) return null;
            if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath;
            return `${this.imageRouteUrl}/${imagePath.replace(/^\//, '')}`;
        },

        selectProduct(product) {
            if (product.sizes && Array.isArray(product.sizes) && product.sizes.length > 0) {
                this.selectedProductForSize = product;
                this.selectedSize = product.sizes[0];
                this.openModal('selectSize');
            } else {
                const sizeName = product.size || product.talla || null;
                this.addToCart(product, null, sizeName);
            }
        },

        addFirstMatch() {
            if (!this.searchQuery.trim()) return;
            const q = this.searchQuery.trim().toLowerCase();
            const exactMatch = this.products.find(p => p.barcode && p.barcode.toString().toLowerCase() === q);

            if (exactMatch) {
                this.selectProduct(exactMatch);
            } else if (this.filteredProducts.length > 0) {
                this.selectProduct(this.filteredProducts[0]);
            }
            this.searchQuery = '';
        },

        confirmSizeSelection() {
            if (this.selectedProductForSize && this.selectedSize) {
                this.addToCart(this.selectedProductForSize, this.selectedSize);
            }
        },

        addToCart(product, sizeObj = null, customSizeName = null) {
            let sizeName = customSizeName;
            let sizeId = null;
            let maxStock = product.stock;

            if (sizeObj) {
                if (typeof sizeObj === 'object') {
                    sizeName = sizeObj.name || sizeObj.size || sizeObj.talla || null;
                    sizeId = sizeObj.id || null;
                    if (sizeObj.stock !== undefined) maxStock = sizeObj.stock;
                } else {
                    sizeName = sizeObj;
                }
            }

            const itemKey = sizeName ? `${product.id}-${sizeName}` : `${product.id}`;
            const existing = this.cart.find(i => i.key === itemKey);

            if (existing) {
                if (existing.qty < maxStock) {
                    existing.qty++;
                } else {
                    this.showAlert('warning', 'Stock Límite', `No hay más stock disponible para esta talla.`);
                }
            } else {
                if (maxStock <= 0) {
                    this.showAlert('error', 'Sin Stock', 'Este producto no cuenta con inventario disponible.');
                    return;
                }

                this.cart.push({
                    key: itemKey,
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image_path: product.image_path || null,
                    size: sizeName,
                    size_id: sizeId,
                    qty: 1,
                    max_stock: maxStock
                });
            }

            this.selectedProductForSize = null;
            this.selectedSize = null;
            this.closeModal('selectSize');
        },

        updateQty(index, changeVal) {
            const item = this.cart[index];
            if (!item) return;

            if (changeVal > 0) {
                if (item.qty < item.max_stock) {
                    item.qty++;
                } else {
                    this.showAlert('warning', 'Stock Límite', 'No hay suficiente inventario disponible.');
                }
            } else {
                item.qty--;
                if (item.qty <= 0) this.cart.splice(index, 1);
            }
        },

        async processSale() {
            if (this.cart.length === 0) return;

            const currentTotal = this.total;

            if (this.paymentMethod === 'cash') {
                const received = parseFloat(this.amountReceived) || 0;
                if (received < currentTotal) {
                    this.showAlert('warning', 'Efectivo Insuficiente', 'El monto recibido es menor al total de la venta.');
                    return;
                }
            }

            if (this.referenceRequired && !this.reference.trim()) {
                this.showAlert('warning', 'Referencia Requerida', 'Ingresa el número de autorización o referencia de la transacción.');
                return;
            }

            const paid = this.paymentMethod === 'cash' ? (parseFloat(this.amountReceived) || currentTotal) : currentTotal;
            const changeAmount = this.paymentMethod === 'cash' ? Math.max(0, paid - currentTotal) : 0;

            Swal.fire({
                ...this.swalConfig,
                title: 'Procesando venta...',
                text: 'Por favor espera un momento',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const payload = {
                items: this.cart.map(item => ({
                    id: item.id,
                    qty: item.qty,
                    price: item.price,
                    size: item.size,
                    size_id: item.size_id
                })),
                payment_method: this.paymentMethod,
                total: currentTotal,
                received_amount: paid,
                change: changeAmount,
                reference: this.referenceRequired ? this.reference.trim() : null
            };

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch('/pos/procesar-venta', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && (data.status === 'success' || data.success)) {
                    if (data.updatedProducts) {
                        Object.keys(data.updatedProducts).forEach(id => {
                            const localProduct = this.products.find(p => p.id == id);
                            if (localProduct) {
                                localProduct.stock = data.updatedProducts[id].stock;
                                localProduct.sizes = data.updatedProducts[id].sizes;
                            }
                        });
                    }

                    this.lastTicket = {
                        items: [...this.cart],
                        total: currentTotal,
                        paymentMethod: this.paymentMethod,
                        amountReceived: paid,
                        amountPaid: paid,
                        change: changeAmount,
                        reference: payload.reference
                    };

                    this.cart = [];
                    this.amountReceived = '';
                    this.change = 0;
                    this.reference = '';
                    this.closeModal('checkoutModal');

                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#0f172a',
                        color: '#f8fafc'
                    }).fire({ icon: 'success', title: '¡Venta procesada con éxito!' });

                    this.openModal('ticketModal');

                } else {
                    this.showAlert('error', 'Error en Venta', data.message || 'No se pudo procesar la venta.', { confirmButtonColor: '#ef4444' });
                }
            } catch (error) {
                console.error('Error al procesar la venta:', error);
                this.showAlert('error', 'Error de Conexión', 'Hubo un fallo de comunicación con el servidor.', { confirmButtonColor: '#ef4444' });
            }
        },

        // --- FUNCIÓN DE IMPRESIÓN DEL TICKET ---
printTicket() {
    if (!this.lastTicket || !this.lastTicket.items || this.lastTicket.items.length === 0) {
        this.showAlert('warning', 'Sin datos', 'No hay datos de la última venta para imprimir.');
        return;
    }

    // Formateadores de moneda y fecha
    const formatMoney = (val) => '$' + parseFloat(val || 0).toFixed(2);
    const dateStr = new Date().toLocaleString('es-MX', { 
        dateStyle: 'short', 
        timeStyle: 'short' 
    });

    // Generar filas de productos
    const itemsHtml = this.lastTicket.items.map(item => {
        const itemTotal = parseFloat(item.price) * parseInt(item.qty);
        const sizeText = item.size ? ` (${item.size})` : '';
        return `
            <div class="item-row">
                <div class="item-title">${item.name}${sizeText}</div>
                <div class="row-flex">
                    <span>${item.qty} x ${formatMoney(item.price)}</span>
                    <span class="bold">${formatMoney(itemTotal)}</span>
                </div>
            </div>
        `;
    }).join('');

    // Traducción de método de pago
    const methodNames = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia' };
    const payMethodText = methodNames[this.lastTicket.paymentMethod] || 'Efectivo';

    // Plantilla HTML optimizada para impresión térmica de 58mm
    const ticketHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Ticket de Venta</title>
            <style>
                @page { 
                    margin: 0; 
                    size: auto; 
                }
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                    font-family: 'Courier New', Courier, monospace, sans-serif;
                }
                body {
                    width: 58mm;
                    padding: 4px 6px;
                    font-size: 11px;
                    line-height: 1.25;
                    color: #000;
                    background: #fff;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .bold { font-weight: bold; }
                
                .header {
                    margin-bottom: 6px;
                }
                .brand {
                    font-size: 13px;
                    font-weight: bold;
                }
                .subtitle {
                    font-size: 9px;
                }
                
                .dashed-line {
                    border-bottom: 1px dashed #000;
                    margin: 5px 0;
                }
                
                .row-flex {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .item-row {
                    margin-bottom: 4px;
                }
                .item-title {
                    font-weight: bold;
                    word-break: break-all;
                }
                
                .totals {
                    margin-top: 4px;
                }
                .totals .row-flex {
                    margin-bottom: 2px;
                }
                .total-main {
                    font-size: 13px;
                    margin: 3px 0;
                }
                
                .footer {
                    margin-top: 8px;
                    font-size: 10px;
                }
            </style>
        </head>
        <body>
            <div class="header text-center">
                <div class="brand">SCGI NEGOCIOS</div>
                <div class="subtitle">Sistema de Control y Gestión</div>
                <div style="font-size: 10px; margin-top: 2px;">${dateStr}</div>
            </div>

            <div class="dashed-line"></div>

            <div class="items-list">
                ${itemsHtml}
            </div>

            <div class="dashed-line"></div>

            <div class="totals">
                <div class="row-flex">
                    <span>Subtotal:</span>
                    <span>${formatMoney(this.lastTicket.total)}</span>
                </div>
                <div class="row-flex total-main bold">
                    <span>TOTAL:</span>
                    <span>${formatMoney(this.lastTicket.total)}</span>
                </div>
                
                <div class="dashed-line"></div>
                
                <div class="row-flex">
                    <span>Pago (${payMethodText}):</span>
                    <span>${formatMoney(this.lastTicket.amountReceived || this.lastTicket.total)}</span>
                </div>
                ${this.lastTicket.paymentMethod === 'cash' ? `
                    <div class="row-flex">
                        <span>Cambio:</span>
                        <span>${formatMoney(this.lastTicket.change)}</span>
                    </div>
                ` : ''}
                ${this.lastTicket.reference ? `
                    <div class="row-flex" style="font-size: 9px;">
                        <span>Ref/Autorización:</span>
                        <span>${this.lastTicket.reference}</span>
                    </div>
                ` : ''}
            </div>

            <div class="dashed-line"></div>

            <div class="footer text-center">
                <p class="bold">¡Gracias por su compra!</p>
                <p style="font-size: 8px; margin-top: 2px;">Conserve este ticket para cualquier aclaración o devolución.</p>
            </div>
        </body>
        </html>
    `;

    // Reutilizar o crear el iframe oculto
    let iframe = document.getElementById('posPrintFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'posPrintFrame';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0px';
        iframe.style.height = '0px';
        iframe.style.border = 'none';
        document.body.appendChild(iframe);
    }

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write(ticketHTML);
    doc.close();

    // Función encargada de ejecutar la impresión
    const triggerPrint = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    };

    // Disparar por evento de carga o fallback si ya está cargado
    if (doc.readyState === 'complete') {
        triggerPrint();
    } else {
        iframe.onload = triggerPrint;
    }
}
    }));
});