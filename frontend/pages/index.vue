<template>
  <v-app class="grey">
    <v-container>
      <!-- <v-container fluid> -->
      <Heading>SEARCH PROBLEMS</Heading>
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
          hid: 'Codang Search Problems',
          name: 'Codang Search Problems',
          content: 'Codang Search Problems',
        },
      ],
    }
  },
}
</script>
