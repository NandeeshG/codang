<template>
  <v-app class="teal">
    <v-container>
      <!-- <v-container fluid> -->
      <Heading>ABOUT CODANG</Heading>
      <v-row justify="center">
        <v-col cols="12"> <Tags /> </v-col>
      </v-row>
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
          hid: 'Codang About Page',
          name: 'Codang About Page',
          content: 'Codang About Page',
        },
      ],
    }
  },
}
</script>

<style></style>
