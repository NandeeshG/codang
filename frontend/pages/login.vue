<!-- STOP THIS PAGE NO NEED -->

<template>
  <v-app class="grey">
    <v-container>
      <!-- <v-container fluid> -->
      <Heading>LOGIN</Heading>
      <v-row justify="center">
        <v-col cols="12">
          <v-form ref="form" class="mt-13">
            <v-row justify="center">
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="newStudent.name"
                  :rules="[rules.required, rules.name]"
                  dense
                  outlined
                  label="Name"
                  class="ma-n2"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  :rules="[rules.required, rules.roll]"
                  dense
                  outlined
                  label="Roll No."
                  class="ma-n2"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  :rules="[rules.required, rules.class]"
                  dense
                  outlined
                  label="Class"
                  class="ma-n2"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  :rules="[rules.required, rules.section]"
                  dense
                  outlined
                  label="Section"
                  class="ma-n2"
                />
              </v-col>
            </v-row>
            <v-row justify="center">
              <v-col cols="12" sm="8">
                <v-btn block type="submit"> ADD STUDENT </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-col>
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
