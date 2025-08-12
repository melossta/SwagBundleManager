<!--<template>-->
<!--  <div>Hello world</div>-->
<!--</template>-->

<!--<script>-->
<!--export default {-->
<!--  name: 'sw-bundle-index'-->
<!--};-->
<!--</script>-->
<template>
  <sw-page>
    <template #smart-bar-header>
      <h2>{{ $tc('swag-bundle.general.mainMenuItemGeneral') }}</h2>
      <h1>hello</h1>
    </template>

    <template #content>
      <sw-card-view>
        <sw-card :title="$tc('swag-bundle.general.welcomeCard')">
          <sw-container columns="1fr" gap="16px">
            <p>{{ $tc('swag-bundle.general.welcomeMessage') }}</p>

            <sw-button
                variant="primary"
                @click="showNotification"
                :isLoading="isLoading"
            >
              {{ $tc('sw-bundle.general.actionButton') }}
            </sw-button>
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
      isLoading: false
    };
  },
  methods: {
    showNotification() {
      this.isLoading = true;

      this.$root.$emit('notification', {
        variant: 'success',
        title: this.$tc('swag-bundle.notification.title'),
        message: this.$tc('swag-bundle.notification.message')
      });

      setTimeout(() => {
        this.isLoading = false;
      }, 1000);
    }
  }
};
</script>
