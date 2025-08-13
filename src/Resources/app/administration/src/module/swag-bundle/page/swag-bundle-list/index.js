import template from './swag-bundle-list.html.twig';

const {Component}= Shopware;
const {Criteria}= Shopware.Data;

Component.register('swag-bundle-list',{
    template,

    inject:[
        'repositoryFactory'
    ],
    data(){
        return{
            repository:null,
            bundles:null
        }
    },
    metaInfo(){
        return{
            title: this.$createTitle()
        };
    },
    computed:{
        columns(){
            return this.getColumns();
        }
    },
    created(){
        this.createdComponent();
    },
    methods:{
        createdComponent(){
            this.repository=this.repositoryFactory.create('swag_bundle')
            this.repository.search(new Criteria(),Shopware.Context.api).then((result)=>{
                this.bundles=result;
            });
        },
        getColumns(){
            return[
                {
                    property:'name',
                    label:this.$tc('swag-bundle.list.columnName'),
                    // routerLink:'swag.bundle.detail', //issue stays that the swag.bundle.detail is still not set up and if i use it the name is not showing so i commented it out
                    inlineEdit:'string',
                    allowResize:true,
                    primary:true
                },
                {
                    property: 'discount',
                    label: this.$tc('swag-bundle.list.columnDiscount'),
                    inlineEdit:'number',
                    allowResize: true
                },
                {
                    property: 'discountType',
                    label: this.$tc('swag-bundle.list.columnDiscountType'),
                    allowResize: true
                }
            ]
        }
    }
})