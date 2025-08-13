/*import './page/swag-bundle-index';
import enGB from './snippet/en-GB.json';

Shopware.Locale.extend('en-GB', enGB);

Shopware.Module.register('swag-bundle', {
    type: 'core',
    name: 'swag-bundle',
    title: 'swag-bundle.general.mainMenuItemGeneral',
    description: 'swag-bundle.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-object-bundle', // You can use any icon here
    routePrefixPath: 'swag.bundle',
    routes: {
        index: {
            component: 'swag-bundle-index',
            path: 'index'
        }
    },
    navigation: [
        {
            id: 'swag-bundle',
            label: 'swag-bundle.general.mainMenuItemGeneral',
            color: '#ff3d58',
            icon: 'regular-shopping-bag',
            path: 'swag.bundle.index',
            // ⛔ Removed parent!
            position: 80,
            privilege: null
        }
    ]
});
*/
// import './page/swag-bundle-index';
import './page/swag-bundle-list';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
const {Module} = Shopware;
Shopware.Module.register('swag-bundle',
    {
     type:'core',
     name:'bundle',
     title:'swag-bundle.general.mainMenuItemGeneral',
     description:'swag-bundle.general.descriptionTextModule',
     color:'#ff3d58',
     icon:'regular-shopping-bag',
    routePrefixPath: 'swag.bundle',

        snippets:{
            'de-DE':deDE,
            'en-GB':enGB
        },
        routes: {
            index: {
                component: 'swag-bundle-list',
                path: 'index'
            }
        },
        navigation:[{
         label:'swag-bundle.general.mainMenuItemGeneral',
            color:'#ff3d58',
            path:"swag.bundle.index",
            icon:"regular-shopping-bag",
            position:100
        }]
    }



)