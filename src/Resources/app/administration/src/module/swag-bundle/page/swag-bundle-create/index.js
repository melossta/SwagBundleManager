const {Component} = Shopware;

Component.extend('swag-bundle-create','swag-bundle-detail',{
    methods:{
        getBundle() {
            this.bundle = this.repository.create(Shopware.Context.api);
            this.bundle.discountType = 'absolute'; // or 'percent', whichever you want default
        },

        onClickSave() {
            this.isLoading = true;

            // Attach the selected product IDs to the bundle's products association
            this.bundle.products = this.selectedProductIds.map(productId => {
                return {
                    productId, // key for the mapping table
                    id: productId // Shopware still needs the ID for reference
                };
            });

            console.log('Final bundle payload before save:', JSON.parse(JSON.stringify(this.bundle)));

            this.repository.save(this.bundle, Shopware.Context.api)
                .then(() => {
                    this.getBundle();
                    this.isLoading = false;
                    this.processSuccess = true;
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$tc('swag-bundle.detail.saveError'),
                        message: exception
                    });
                });
        }
    }
});