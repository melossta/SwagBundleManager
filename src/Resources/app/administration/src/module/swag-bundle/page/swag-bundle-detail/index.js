import template from './swag-bundle-detail.html.twig';

const { Component, Mixin } = Shopware;

Component.register('swag-bundle-detail', {
    template,
    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            discountTypeLocal: 'absolute', // default selection
            bundle: null,
            assignedProducts: [],
            selectedProductId: null,
            products: [],
            isLoading: false,
            processSuccess: false,
            options: [
                { value: 'absolute', name: 'Absolute' },
                { value: 'percent', name: 'Percent' }
            ]
        };
    },

    computed: {

    },


    created() {
        this.repository = this.repositoryFactory.create('swag_bundle');
        this.productRepository = this.repositoryFactory.create('product');

        this.loadProducts();
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.$route.params.id) {
                this.getBundle();
            } else {
                // ✅ New bundle creation
                this.bundle = this.repository.create(Shopware.Context.api);
                this.discountTypeLocal = 'absolute'; // or leave null until user selects
            }
        },
        loadProducts() {
            const Criteria = Shopware.Data.Criteria;
            const criteria = new Criteria(1, 50);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.productRepository.search(criteria, Shopware.Context.api).then(result => {
                this.products = result;
            });
        },

        getBundle() {
            const Criteria = Shopware.Data.Criteria;
            const criteria = new Criteria();
            criteria.addAssociation('products');

            this.repository.get(this.$route.params.id, Shopware.Context.api, criteria).then((entity) => {
                this.bundle = entity;

                // 👇 keep local in sync with DB (fallback to default)
                this.discountTypeLocal = this.bundle.discountType || 'absolute';

                if (this.bundle.products && this.bundle.products.length > 0) {
                    this.assignedProducts = this.bundle.products;
                } else {
                    this.assignedProducts = [];
                }
            });
        },

        onClickSave() {
            this.isLoading = true;

            // 👇 push local value back onto the entity before saving
            this.bundle.discountType = this.discountTypeLocal;

            // map assigned products (unchanged)
            this.bundle.products = this.assignedProducts.map(p => ({ id: p.id }));

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

        ,
        addProductToAssigned() {
            if (!this.selectedProductId) return;

            const product = this.products.find(p => p.id === this.selectedProductId);
            if (product && !this.assignedProducts.some(ap => ap.id === product.id)) {
                this.assignedProducts.push(product);
                console.log('Added product:', product.name);
            }

            // reset dropdown
            this.selectedProductId = null;
        },
        removeProductFromAssigned(productId) {
            this.assignedProducts = this.assignedProducts.filter(p => p.id !== productId);
            console.log('Removed product:', productId);
        },
        saveFinish() {
            this.processSuccess = false;
        }
    }
});
