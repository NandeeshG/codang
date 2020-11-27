<template>
  <v-snackbar
    v-model="snackbar.display"
    timeout="2000"
    bottom
    left
    rounded="r-xl"
    :color="snackbar.color"
    transition="slide-x-transition"
  >
    {{ snackbar.message }}
  </v-snackbar>
</template>

<script>
export default {
  props: {
    snackbar: {
      type: Object,
      default() {
        return {
          display: false,
          color: 'red',
          message: 'Alert!',
        }
      },
    },
  },
}
</script>

<style></style>
