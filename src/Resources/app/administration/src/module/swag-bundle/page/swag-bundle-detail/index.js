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
            bundle: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            products: [],
            assignedProducts: [],
            selectedProductId: null
        };
    },

    computed: {
        options() {
            return [
                { value: 'absolute', name: this.$tc('swag-bundle.detail.absoluteText') },
                { value: 'percent', name: this.$tc('swag-bundle.detail.absolutePercent') }
            ];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('swag_bundle');
        this.productRepository = this.repositoryFactory.create('product');

        this.loadProducts();
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getBundle();
        },
        loadProducts() {
            const Criteria = Shopware.Data.Criteria;
            const criteria = new Criteria(1, 50);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.productRepository.search(criteria, Shopware.Context.api).then(result => {
                this.products = result;
            });
        },

        onClickSave() {
            this.isLoading = true;

            // Map assigned products to backend format
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
        },
        getBundle() {
            const Criteria = Shopware.Data.Criteria;
            const criteria = new Criteria();
            criteria.addAssociation('products');

            this.repository.get(this.$route.params.id, Shopware.Context.api, criteria).then((entity) => {
                this.bundle = entity;

                if (this.bundle.products && this.bundle.products.length > 0) {
                    this.assignedProducts = this.bundle.products;
                    console.log('Assigned products loaded:', this.assignedProducts);
                } else {
                    console.log('No products assigned to this bundle.');
                    this.assignedProducts = [];
                }
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
