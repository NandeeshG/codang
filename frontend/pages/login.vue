<!-- STOP THIS PAGE NO NEED -->

<template>
  <v-app class="grey">
    <v-container>
      <!-- <v-container fluid> -->
      <Header> Use of this login page is not final rn. </Header>
    </v-container>
    <Snackbar :snackbar="snackbar" />
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      snackbar: {
        display: false,
        color: 'red',
        message: 'none',
      },
    }
  },
  head() {
    return {
      title: 'Codang',
      meta: [
        // hid is used as unique identifier. Do not use `vmid` for it as it will not work
        {
          hid: 'Codang Login Page',
          name: 'Codang Login Page',
          content: 'Codang Login Page',
        },
      ],
    }
  },
}
</script>

<style></style>
