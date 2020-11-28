<template>
  <v-app>
    <!--
    <v-system-bar app status color="indigo lighten-1" height="20">
      <strong> Status Bar </strong>
      <v-spacer />
      <v-icon>mdi-account</v-icon>
      <v-icon>mdi-cloud-check</v-icon>
      <v-icon>mdi-wifi-strength-4</v-icon>
    </v-system-bar>
    -->

    <v-app-bar app color="black">
      <v-btn icon @click="drawer = !drawer">
        <v-icon large>mdi-menu</v-icon>
      </v-btn>
      <v-spacer></v-spacer>
      <v-icon large color="primary">CODANG</v-icon>
      <v-spacer></v-spacer>
      <v-avatar size="46" color="grey">
        <router-link to="/profile">
          <v-img src="chef.jpg" contain height="50px" width="50px"> </v-img>
        </router-link>
      </v-avatar>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app>
      <!--<v-navigation-drawer v-model="drawer" app floating temporary>-->
      <v-list-item>
        <v-list-item-content>
          <v-avatar size="100">
            <img src="../static/chef.jpg" />
          </v-avatar>
          <v-list-item-title class="title d-flex justify-center mt-2"
            >Hi {{ vuexUsername }}!</v-list-item-title
          >
          <v-list-item-action>
            <!--<v-btn v-if="userIsLoggedIn" block link to="/logout">
              Logout
            </v-btn>
            <v-btn v-else block link to="/login"> Login </v-btn>-->
            <v-btn v-if="userIsLoggedIn" block @click="loginDialog = true">
              Logout
            </v-btn>
            <v-btn v-else block @click="loginDialog = true"> Login </v-btn>
          </v-list-item-action>
        </v-list-item-content>
      </v-list-item>

      <v-divider />

      <v-list dense nav>
        <v-list-item link to="/profile">
          <v-list-item-icon><v-icon>mdi-arrow-right</v-icon></v-list-item-icon>
          <v-list-item-content>My Profile</v-list-item-content>
        </v-list-item>
        <v-list-item link to="/">
          <v-list-item-icon><v-icon>mdi-arrow-right</v-icon></v-list-item-icon>
          <v-list-item-content>Search Problems</v-list-item-content>
        </v-list-item>
        <v-list-item link to="/tag">
          <v-list-item-icon><v-icon>mdi-arrow-right</v-icon></v-list-item-icon>
          <v-list-item-content>Select Tags</v-list-item-content>
        </v-list-item>
        <v-list-item link to="/problem">
          <v-list-item-icon><v-icon>mdi-arrow-right</v-icon></v-list-item-icon>
          <v-list-item-content>Last Problem</v-list-item-content>
        </v-list-item>
        <v-list-item link to="/about">
          <v-list-item-icon><v-icon>mdi-arrow-right</v-icon></v-list-item-icon>
          <v-list-item-content>About Codang</v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main class="blue-grey">
      <v-dialog v-model="loginDialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            <span v-if="userIsLoggedIn === false" class="headline"
              >LOGIN DETAILS</span
            >
            <span v-else class="headline">DO YOU REALLY WANT TO LOGOUT?</span>
          </v-card-title>
          <v-card-text v-if="userIsLoggedIn === false">
            <v-text-field label="Username" required></v-text-field>
            <!--<v-col cols="12">
                  <v-text-field
                    label="Password*"
                    type="password"
                    required
                  ></v-text-field>
                </v-col>-->
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="loginDialog = false">
              Cancel
            </v-btn>
            <v-btn
              v-if="userIsLoggedIn === false"
              color="blue darken-1"
              text
              @click="loginDialog = false"
            >
              Login
            </v-btn>
            <v-btn
              v-else
              color="blue darken-1"
              text
              @click="loginDialog = false"
            >
              Logout
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <nuxt />
    </v-main>

    <v-footer class="black" app fixed inset>
      <v-chip class="ma-n1" link to="/about"> Made by Nandeesh </v-chip>
      <v-btn
        v-for="(sm, id) in social"
        :key="id"
        icon
        :href="sm.link"
        target="_blank"
        small
        class="ml-2"
      >
        <v-icon>{{ sm.icon }}</v-icon>
      </v-btn>

      <v-spacer />

      <v-chip class="ma-n1" link to="/"> CODANG </v-chip>

      <v-spacer />
      <v-chip class="ma-n1" href="https://www.codechef.com/" target="_blank">
        CodeChef-API-{{ new Date().getFullYear() }}
      </v-chip>
    </v-footer>
  </v-app>
</template>

<script>
/* eslint-disable no-console */

export default {
  name: 'Default',
  data() {
    return {
      loginDialog: false,
      drawer: false,
      social: [
        { icon: 'mdi-github', link: 'https://www.github.com/NandeeshG/codang' },
        {
          icon: 'mdi-linkedin',
          link: 'https://www.linkedin.com/in/nandeesh-gupta-05b43014a/',
        },
      ],
      cols_val: 12,
      sm_val: 6,
      md_val: 4,
      lg_val: 3,
    }
  },
  computed: {
    vuexUsername() {
      return this.$store.state.username
    },
    userIsLoggedIn() {
      return this.$store.state.loggedIn
    },
  },
}
</script>

<style></style>
