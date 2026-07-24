document.addEventListener('alpine:init', () => {
    Alpine.data('posSystem', (productsData = [], imageRouteUrl = '') => ({
        // --- ESTADO Y VARIABLES ---
        products: productsData,
        imageRouteUrl: imageRouteUrl,
        searchQuery: '',
        cart: [],

        // Estado de Modales
        modals: {
            selectSize: false,
            checkoutModal: false,
            ticketModal: false
        },

        selectedProductForSize: null,
        selectedSize: null,

        // Propiedades de cobro
        paymentMethod: 'cash',
        amountReceived: '', // Iniciado en vacío para que el cajero escriba libremente
        change: 0,

        lastTicket: {
            items: [],
            total: 0,
            paymentMethod: 'cash',
            amountReceived: 0,
            amountPaid: 0,
            change: 0
        },

        // Configuración base de SweetAlert2 (Tema oscuro)
        swalConfig: {
            background: '#0f172a', // Slate-900
            color: '#f8fafc',
            confirmButtonColor: '#4f46e5', // Indigo-600
            cancelButtonColor: '#64748b',  // Slate-500
        },

        // Filtra productos por nombre o código de barras/SKU
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

        // Total general del carrito
        get total() {
            return this.cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.qty), 0);
        },

        showAlert(icon, title, text, extraOpts = {}) {
            return Swal.fire({
                ...this.swalConfig,
                icon,
                title,
                text,
                ...extraOpts
            });
        },

        openModal(name) {
            if (this.modals.hasOwnProperty(name)) {
                this.modals[name] = true;
                if (name === 'checkoutModal') {
                    // Dejamos el campo vacío para que el usuario ingrese el monto con el que paga
                    this.amountReceived = ''; 
                    this.change = 0;
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
            if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                return imagePath;
            }
            return `${this.imageRouteUrl}/${imagePath.replace(/^\//, '')}`;
        },

        // --- MÉTODOS DE CARRITO Y SELECCIÓN ---

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
                    if (sizeObj.stock !== undefined) {
                        maxStock = sizeObj.stock;
                    }
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
                    this.showAlert('warning', 'Stock Límite', `No hay más stock disponible para la talla ${sizeName || ''}`);
                }
            } else {
                if (maxStock <= 0) {
                    this.showAlert('error', 'Sin Stock', 'Este producto o talla no cuenta con inventario disponible.');
                    return;
                }

                const imgPath = product.image_path || product.image || null;

                this.cart.push({
                    key: itemKey,
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image_path: imgPath,
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
                if (item.qty <= 0) {
                    this.cart.splice(index, 1);
                }
            }
        },

        // --- PROCESAMIENTO DE VENTA ---

        async processSale() {
            if (this.cart.length === 0) return;

            const currentTotal = this.total;
            const paid = this.paymentMethod === 'cash' ? (parseFloat(this.amountReceived) || 0) : currentTotal;

            if (this.paymentMethod === 'cash' && paid < currentTotal) {
                this.showAlert('warning', 'Efectivo Insuficiente', 'El monto recibido es menor al total de la venta.');
                return;
            }

            const changeAmount = Math.max(0, paid - currentTotal);

            const confirm = await this.showAlert('question', '¿Confirmar cobro?', `Total: $${currentTotal.toFixed(2)} | Cambio: $${changeAmount.toFixed(2)}`, {
                showCancelButton: true,
                confirmButtonText: 'Sí, cobrar',
                cancelButtonText: 'Cancelar'
            });

            if (!confirm.isConfirmed) return;

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
                change: this.paymentMethod === 'cash' ? changeAmount : 0
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
                        change: changeAmount
                    };

                    this.cart = [];
                    this.amountReceived = '';
                    this.change = 0;
                    this.closeModal('checkoutModal');

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#0f172a',
                        color: '#f8fafc'
                    });

                    Toast.fire({
                        icon: 'success',
                        title: '¡Venta procesada con éxito!'
                    });

                    this.openModal('ticketModal');

                } else {
                    this.showAlert('error', 'Error en Venta', data.message || 'No se pudo procesar la venta.', {
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                console.error('Error al procesar la venta:', error);
                this.showAlert('error', 'Error de Conexión', 'Hubo un fallo de comunicación con el servidor.', {
                    confirmButtonColor: '#ef4444'
                });
            }
        },

        // --- IMPRESIÓN ---

        printTicket() {
            const ticketElement = document.getElementById('printableTicket');
            if (!ticketElement) {
                window.print();
                return;
            }

            const iframe = document.createElement('iframe');
            Object.assign(iframe.style, {
                position: 'fixed',
                right: '0',
                bottom: '0',
                width: '0px',
                height: '0px',
                border: 'none'
            });
            
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Ticket de Venta</title>
                    <style>
                        @page { size: 80mm auto; margin: 0; }
                        body { 
                            font-family: monospace; 
                            width: 78mm; 
                            margin: 0 auto; 
                            padding: 8px; 
                            font-size: 11px;
                            color: #000;
                            background: #fff;
                        }
                        * { box-sizing: border-box; }
                    </style>
                </head>
                <body>
                    ${ticketElement.innerHTML}
                </body>
                </html>
            `);
            doc.close();

            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                setTimeout(() => document.body.removeChild(iframe), 1000);
            }, 300);
        }
    }));
});