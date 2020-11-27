<template>
  <v-app class="grey">
    <Heading> SEARCH PROBLEMS </Heading>
    <v-container>
      <v-row justify="center">
        <v-col cols="12"> <Tags /> </v-col>
      </v-row>
    </v-container>

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
}
</script>
