import template from './swag-bundle-detail.html.twig';

const {Component,Mixin}=Shopware;

Component.register('swag-bundle-detail', {
    template,
    inject:[
        'repositoryFactory'
    ],

    mixins:[
        Mixin.getByName('notification')
    ],

    metaInfo(){
        return {
            title: this.getByName('notification'),
        };
    },

    data(){
        return {
            bundle:null,
            isLoading:false,
            processSuccess:false,
            repository:null,
            products: [], // holds all products to choose from
            selectedProductIds: [] // holds selected product IDs
        }
    },
    computed:{
        options(){
            return[
                {value:'absolute',name:this.$tc('swag-bundle.detail.absoluteText')},
                {value: 'percent',name:this.$tc('swag-bundle.detail.absolutePercent')}
            ];
        }
    },

    created(){
        this.repository = this.repositoryFactory.create('swag_bundle');
        this.productRepository = this.repositoryFactory.create('product');

        this.loadProducts();
        this.createdComponent();
    },

    methods:{
        createdComponent() {
            this.getBundle();
        },
        getBundle() {
            this.repository.get(this.$route.params.id, Shopware.Context.api).then((entity) => {
                this.bundle = entity;

                // If bundle already has products, store their IDs
                if (this.bundle.products) {
                    this.selectedProductIds = this.bundle.products.map(p => p.id);
                }
            });
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
,
        onProductChange() {
            console.log('Selected product IDs:', this.selectedProductIds);

            // Optional: log product names for debugging
            const selectedNames = this.products
                .filter(p => this.selectedProductIds.includes(p.id))
                .map(p => p.name);
            console.log('Products added to bundle:', selectedNames);
        },
        saveFinish() {
            this.processSuccess = false;
        }
    }

})