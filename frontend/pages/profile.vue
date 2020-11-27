<template>
  <v-app class="grey">
    <v-container>
      <!-- <v-container fluid> -->
      <Heading>MY PROFILE</Heading>
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
          hid: 'Codang Profile Page',
          name: 'Codang Profile Page',
          content: 'Codang Profile Page',
        },
      ],
    }
  },
}
</script>

<style></style>
