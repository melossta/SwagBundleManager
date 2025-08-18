const {Component} = Shopware;

Component.extend('swag-bundle-create','swag-bundle-detail',{
    methods:{
        getBundle() {
            this.bundle = this.repository.create(Shopware.Context.api);
            this.bundle.discountType = 'absolute';
        },

        onClickSave() {
            this.isLoading = true;


            this.bundle.products = this.assignedProducts.map(p => ({ id: p.id }));

            this.repository.save(this.bundle, Shopware.Context.api).then(() => {
                this.getBundle();
                this.isLoading = false;
                this.processSuccess = true;
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$tc('swag-bundle.detail.saveError'),
                    message: exception
                });
            });
        }
    }
});