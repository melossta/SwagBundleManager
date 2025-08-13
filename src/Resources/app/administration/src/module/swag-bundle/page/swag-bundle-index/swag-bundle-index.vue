
<!--<template>-->
<!--  <sw-page>-->
<!--    <template #smart-bar-header>-->
<!--      <h2>{{ $tc('swag-bundle.general.mainMenuItemGeneral') }}</h2>-->
<!--      <h1>hello</h1>-->
<!--    </template>-->

<!--    <template #content>-->
<!--      <sw-card-view>-->
<!--        <sw-card :title="$tc('swag-bundle.general.welcomeCard')">-->
<!--          <sw-container columns="1fr" gap="16px">-->
<!--            <p>{{ $tc('swag-bundle.general.welcomeMessage') }}</p>-->

<!--            <sw-button-->
<!--                variant="primary"-->
<!--                @click="showNotification"-->
<!--                :isLoading="isLoading"-->
<!--            >-->
<!--              {{ $tc('swag-bundle.general.actionButton') }}-->
<!--            </sw-button>-->
<!--          </sw-container>-->
<!--        </sw-card>-->
<!--      </sw-card-view>-->
<!--    </template>-->
<!--  </sw-page>-->
<!--</template>-->

<!--<script>-->
<!--export default {-->
<!--  name: 'swag-bundle-index',-->
<!--  data() {-->
<!--    return {-->
<!--      isLoading: false-->
<!--    };-->
<!--  },-->
<!--  methods: {-->
<!--    showNotification() {-->
<!--      this.isLoading = true;-->

<!--      this.$root.$emit('notification', {-->
<!--        variant: 'success',-->
<!--        title: this.$tc('swag-bundle.notification.title'),-->
<!--        message: this.$tc('swag-bundle.notification.message')-->
<!--      });-->

<!--      setTimeout(() => {-->
<!--        this.isLoading = false;-->
<!--      }, 1000);-->
<!--    }-->
<!--  }-->
<!--};-->
<!--</script>-->

<template>
  <sw-page>
    <template #smart-bar-header>
      <h2>{{ $tc('swag-bundle.general.mainMenuItemGeneral') }}</h2>
    </template>

    <template #content>
      <sw-card-view>
        <sw-card :title="$tc('swag-bundle.general.bundleListTitle')">
          <sw-container columns="1fr" gap="16px">
            <div v-if="isLoading">
              {{ $tc('swag-bundle.general.loading') }}...
            </div>

            <div v-else-if="bundles.length === 0">
              {{ $tc('swag-bundle.general.noBundles') }}
            </div>

            <ul v-else class="bundle-list">
              <li v-for="bundle in bundles" :key="bundle.id" class="bundle-item">
                <strong>{{ bundle.name }}</strong> - {{ bundle.discountType }}: {{ bundle.discount }}

                <ul class="product-list" v-if="bundle.products && bundle.products.length">
                  <li v-for="product in bundle.products" :key="product.id" class="product-item">
                    {{ product.translated.name || product.name }}
                  </li>
                </ul>
              </li>
            </ul>
          </sw-container>
        </sw-card>
      </sw-card-view>
    </template>
  </sw-page>
</template>

<script>
export default {
  name: 'swag-bundle-index',

  data() {
    return {
      bundles: [],
      isLoading: false,
    };
  },

  created() {
    this.fetchBundles();
  },

  methods: {
    fetchBundles() {
      this.isLoading = true;

      this.$http.get('/api/search/swag_bundle', {
        params: {
          associations: {
            products: {}
          }
        }
      })
          .then(response => {
            this.bundles = response.data.data;
          })
          .catch(() => {
            this.bundles = [];
          })
          .finally(() => {
            this.isLoading = false;
          });
    }
  }
};
</script>

<style scoped>
.bundle-list {
  list-style: none;
  padding-left: 0;
}

.bundle-item {
  margin-bottom: 1em;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.product-list {
  list-style: disc;
  margin-top: 6px;
  margin-left: 1em;
}

.product-item {
  font-size: 0.9em;
}
</style>
