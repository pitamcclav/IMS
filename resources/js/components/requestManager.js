export default () => ({
    loading: false,
    storeId: '',
    staffId: '',
    isStaffSelectDisabled: true,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    requestDetails: [
        {
            itemId: '',
            variants: [
                {
                    colourId: '',
                    sizeId: '',
                    quantity: 1
                }
            ]
        }
    ],
    colours: {},
    sizes: {},
    
    init() {
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        if (document.querySelector('#store') || document.querySelector('select[name^="itemIds"]')) {
            if (userRole === 'staff' || userRole === 'supervisor') {
                this.staffId = userId;
                this.isStaffSelectDisabled = true;
            } else {
                this.updateStaffFieldAccess(userRole, userId);
            }

            // Initialize items if storeId is already set (edit mode)
            if (this.storeId) {
                this.loadInitialData();
            }
        }
    },

    async loadInitialData() {
        this.loading = true;
        try {
            // Load items for the selected store
            const response = await fetch(`/fetch-items/${this.storeId}`);
            if (!response.ok) throw new Error('Failed to fetch items');
            const items = await response.json();

            // Update items in the DOM
            const itemSelects = document.querySelectorAll('select[x-model*="itemId"]');
            itemSelects.forEach(select => {
                const placeholder = select.options[0];
                select.innerHTML = '';
                select.appendChild(placeholder);
                
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.itemId;
                    option.textContent = item.itemName;
                    select.appendChild(option);
                });
            });

            // Initialize colours and sizes for existing items
            for (const detail of this.requestDetails) {
                if (detail.itemId) {
                    // Load colors for this item
                    const coloursResponse = await fetch(`/api/items/${detail.itemId}/colours`);
                    if (!coloursResponse.ok) throw new Error('Failed to fetch colours');
                    const coloursData = await coloursResponse.json();
                    this.colours[detail.itemId] = coloursData.colours;

                    // Initialize sizes object for this item
                    this.sizes[detail.itemId] = {};

                    // For each variant, load its sizes
                    for (const variant of detail.variants) {
                        if (variant.colourId) {
                            await this.fetchSizesForColour(detail.itemId, variant.colourId);
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: 'Failed to load request data'
            });
        } finally {
            this.loading = false;
        }
    },

    async loadItemVariants(itemId) {
        try {
            const coloursResponse = await fetch(`/api/items/${itemId}/colours`);
            if (!coloursResponse.ok) throw new Error('Failed to fetch colours');
            
            const coloursResult = await coloursResponse.json();
            this.colours[itemId] = coloursResult.colours;

            // Initialize sizes object for this item if it doesn't exist
            if (!this.sizes[itemId]) {
                this.sizes[itemId] = {};
            }
        } catch (error) {
            console.error('Error loading variants:', error);
            throw error;
        }
    },

    updateStaffFieldAccess(userRole, userId) {
        if (userRole === 'admin') {
            this.isStaffSelectDisabled = false;
            return;
        }

        if (userRole === 'manager') {
            const storeSelect = document.getElementById('store');
            if (!storeSelect) return;

            const selectedOption = storeSelect.options[storeSelect.selectedIndex];
            const managerId = selectedOption?.getAttribute('data-manager-id');

            if (managerId === userId) {
                this.isStaffSelectDisabled = false;
                this.staffId = '';
            } else {
                this.isStaffSelectDisabled = true;
                this.staffId = userId;
            }
        }
    },
    
    async onStoreChange(storeId) {
        if (!storeId) return;
        
        this.loading = true;
        this.storeId = storeId;
        
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        this.updateStaffFieldAccess(userRole, userId);
        
        // Reset all item selections
        this.requestDetails.forEach(detail => {
            detail.itemId = '';
            detail.variants = [{ colourId: '', sizeId: '', quantity: 1 }];
        });

        try {
            const response = await fetch(`/fetch-items/${storeId}`);
            if (!response.ok) throw new Error('Failed to fetch items');
            
            const items = await response.json();

            // Update available items in the DOM
            const itemSelects = document.querySelectorAll('select[name^="itemIds"]');
            itemSelects.forEach(select => {
                const placeholder = select.options[0];
                select.innerHTML = '';
                select.appendChild(placeholder);
                
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.itemId;
                    option.textContent = item.itemName;
                    select.appendChild(option);
                });
            });
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message
            });
        } finally {
            this.loading = false;
        }
    },
    
    async onItemChange(itemId, detailIndex) {
        if (!itemId) {
            this.requestDetails[detailIndex].variants = [{ colourId: '', sizeId: '', quantity: 1 }];
            return;
        }
        
        this.loading = true;
        this.requestDetails[detailIndex].itemId = itemId;
        
        try {
            // First fetch colours
            const coloursResponse = await fetch(`/api/items/${itemId}/colours`);
            if (!coloursResponse.ok) throw new Error('Failed to fetch colours');
            
            const coloursResult = await coloursResponse.json();
            if (!coloursResult.success) {
                throw new Error(coloursResult.message || 'Failed to fetch colours');
            }

            this.colours[itemId] = coloursResult.colours;

            // Initialize sizes object for this item if it doesn't exist
            if (!this.sizes[itemId]) {
                this.sizes[itemId] = {};
            }

            // If there's a selected colour, fetch sizes for it
            const selectedVariant = this.requestDetails[detailIndex].variants[0];
            if (selectedVariant.colourId) {
                await this.fetchSizesForColour(itemId, selectedVariant.colourId);
            }

        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message
            });
        } finally {
            this.loading = false;
        }
    },

    async onColourChange(colourId, detailIndex, variantIndex) {
        if (!colourId) return;

        const detail = this.requestDetails[detailIndex];
        if (!detail.itemId) return;

        this.loading = true;
        try {
            await this.fetchSizesForColour(detail.itemId, colourId);
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message
            });
        } finally {
            this.loading = false;
        }
    },

    async fetchSizesForColour(itemId, colourId) {
        try {
            const sizesResponse = await fetch(`/api/items/${itemId}/colours/${colourId}/sizes`);
            if (!sizesResponse.ok) throw new Error('Failed to fetch sizes');
            
            const sizesResult = await sizesResponse.json();
            if (!sizesResult.success) {
                throw new Error(sizesResult.message || 'Failed to fetch sizes');
            }

            // Store sizes for this item and colour combination
            if (!this.sizes[itemId]) {
                this.sizes[itemId] = {};
            }
            this.sizes[itemId][colourId] = sizesResult.sizes;
        } catch (error) {
            console.error('Error:', error);
            throw error; // Re-throw to be handled by the caller
        }
    },
    
    addRequestDetail() {
        this.requestDetails.push({
            itemId: '',
            variants: [
                {
                    colourId: '',
                    sizeId: '',
                    quantity: 1
                }
            ]
        });
    },
    
    removeRequestDetail(index) {
        if (this.requestDetails.length > 1) {
            this.requestDetails.splice(index, 1);
        } else {
            Alpine.store('notifications').add({
                type: 'warning',
                message: 'You must have at least one request item.'
            });
        }
    },
    
    addVariant(detailIndex) {
        this.requestDetails[detailIndex].variants.push({
            colourId: '',
            sizeId: '',
            quantity: 1
        });
    },
    
    removeVariant(detailIndex, variantIndex) {
        const variants = this.requestDetails[detailIndex].variants;
        if (variants.length > 1) {
            variants.splice(variantIndex, 1);
        } else {
            Alpine.store('notifications').add({
                type: 'warning',
                message: 'You must have at least one variant.'
            });
        }
    },
    
    incrementQuantity(detailIndex, variantIndex) {
        this.requestDetails[detailIndex].variants[variantIndex].quantity++;
    },
    
    decrementQuantity(detailIndex, variantIndex) {
        const currentQuantity = this.requestDetails[detailIndex].variants[variantIndex].quantity;
        if (currentQuantity > 1) {
            this.requestDetails[detailIndex].variants[variantIndex].quantity--;
        }
    },
    
    validateRequest() {
        if (!this.storeId) {
            throw new Error('Please select a store.');
        }

        if (!this.staffId) {
            throw new Error('Please select a staff member.');
        }

        const validDetails = this.requestDetails.every(detail => {
            if (!detail.itemId) return false;
            return detail.variants.every(variant => 
                variant.colourId && 
                variant.sizeId && 
                variant.quantity > 0
            );
        });

        if (!validDetails) {
            throw new Error('Please complete all item details with valid variants.');
        }

        return true;
    },
    
    async submitRequest() {
        this.loading = true;
        
        try {
            if (!this.csrfToken) {
                throw new Error('CSRF token not found');
            }

            this.validateRequest();

            const formData = {
                storeId: this.storeId,
                staffId: this.staffId,
                details: this.requestDetails.flatMap(detail => 
                    detail.variants.map(variant => ({
                        itemId: detail.itemId,
                        colourId: variant.colourId,
                        sizeId: variant.sizeId,
                        quantity: variant.quantity
                    }))
                )
            };
            
            const response = await fetch('/api/requests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Server error occurred');
            }

            const result = await response.json();
            
            if (result.success) {
                Alpine.store('notifications').add({
                    type: 'success',
                    message: 'Request submitted successfully!'
                });
                
                if (result.redirect_url) {
                    setTimeout(() => {
                        window.location.href = result.redirect_url;
                    }, 1000);
                }
            } else {
                throw new Error(result.message || 'Failed to submit request');
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message
            });
        } finally {
            this.loading = false;
        }
    },
    
    // New method for the index page
    async updateStatus(url, status) {
        this.loading = true;

        try {
            if (!this.csrfToken) {
                throw new Error('CSRF token not found');
            }

            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status })
            });

            if (!response.ok) throw new Error('Failed to update status');

            const result = await response.json();
            if (result.success) {
                Alpine.store('notifications').add({
                    type: 'success',
                    message: 'Status updated successfully'
                });
                window.location.reload();
            } else {
                throw new Error(result.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error:', error);
            Alpine.store('notifications').add({
                type: 'error',
                message: error.message
            });
        } finally {
            this.loading = false;
        }
    }
});