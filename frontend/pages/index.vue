<template>
  <v-app class="grey">
    <v-container>
      <!-- <v-container fluid> -->
      <Heading>SEARCH PROBLEMS</Heading>
      <v-row justify="center">
        <v-col cols="8">
          <v-autocomplete
            v-model="selectedtags"
            auto-select-first
            chips
            clearable
            deletable-chips
            multiple
            :items="alltags_names"
            eager
          ></v-autocomplete>
        </v-col>
        <v-col cols="4">
          <v-overflow-btn
            v-model="selectedcategory"
            :items="allcategories_names"
            @change="refreshTags"
          >
            Category
          </v-overflow-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="4">
          <v-btn block @click="findProblems(0)"> FIND(using OR) </v-btn>
        </v-col>
        <v-col cols="4">
          <v-btn block @click="findProblems(1)"> FIND(using AND) </v-btn>
        </v-col>
        <v-col cols="4">
          <v-btn block @click="clearProblems"> CLEAR RESULTS </v-btn>
        </v-col>
      </v-row>
      <v-divider />
      <v-subheader v-if="allproblems_details.length > 0" class="black--text"
        >Fetched {{ allproblems_details.length }} problems.</v-subheader
      >
      <v-divider />
      <v-row v-for="(problem, i) in allproblems_details" :key="i">
        <v-col>
          <v-card>
            <v-card-title>{{ problem.name }}</v-card-title>
            <v-card-subtitle>{{ problem.code }}</v-card-subtitle>
            <v-card-text
              >{{ problem.author }} at {{ problem.date }}</v-card-text
            >
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <Snackbar :snackbar="snackbar" />
  </v-app>
</template>

<script>
/* eslint-disable no-console */
export default {
  async asyncData({ $axios }) {
    const alldata = { tags: [], categories: [] }
    alldata.tags = await $axios.$get('/tags')
    alldata.categories = await $axios.$get('/categories')
    return { alldata }
  },
  data() {
    return {
      snackbar: {
        display: false,
        color: 'red',
        message: 'none',
      },
      alltags_names: [],
      allcategories_names: ['All'],
      selectedtags: [],
      selectedcategory: 'All',
      allproblems: {},
      allproblems_details: [],
    }
  },
  mounted() {
    this.alltags_names = []
    this.alldata.tags.forEach((element) => {
      this.alltags_names.push(element.tagname)
    })
    this.alldata.categories.forEach((el) => {
      this.allcategories_names.push(el.name)
    })
  },
  methods: {
    clearProblems() {
      this.allproblems_details = []
      this.selectedtags = []
    },
    async refreshTags() {
      this.alldata.tags = await this.$axios.$get('/tags', {
        params: { category: this.selectedcategory },
      })
      this.selectedtags = []
      this.alltags_names = []
      if (this.alldata.tags[0] === 'empty') {
        this.snackbar.display = true
        this.snackbar.color = 'red'
        this.snackbar.message = 'No data found!'
      } else {
        this.alldata.tags.forEach((element) => {
          this.alltags_names.push(element.tagname)
        })
      }
    },
    async findProblems(arg) {
      if (this.selectedtags.length === 0) {
        this.snackbar.display = true
        this.snackbar.color = 'red'
        this.snackbar.message = 'Select at least one tag.'
      } else if (arg === 0) {
        try {
          // this.allproblems = await this.$axios.get('/problems', {
          //  params: { tags: this.selectedtags },
          // })
          // console.log(this.selectedtags)
          this.allproblems = await this.$axios.get(
            `/problemsOR?${this.selectedtags
              .map((n, index) => `tags[${index}]=${n}`)
              .join('&')}`
          )
          // console.log(this.allproblems.data[0])
          // console.log(this.allproblems.status)

          if (this.allproblems.data[0] === 'empty') {
            this.snackbar.display = true
            this.snackbar.color = 'red'
            this.snackbar.message = 'No data found!'
          } else {
            this.allproblems_details = []
            this.allproblems.data.forEach((el) => {
              this.allproblems_details.push(el)
            })
          }
        } catch (error) {
          console.log(error)
        }
      } else if (arg === 1) {
        try {
          // this.allproblems = await this.$axios.get('/problems', {
          //  params: { tags: this.selectedtags },
          // })
          // console.log(this.selectedtags)
          this.allproblems = await this.$axios.get(
            `/problemsAND?${this.selectedtags
              .map((n, index) => `tags[${index}]=${n}`)
              .join('&')}`
          )
          // console.log(this.allproblems.data[0])
          // console.log(this.allproblems.status)

          if (this.allproblems.data[0] === 'empty') {
            this.snackbar.display = true
            this.snackbar.color = 'red'
            this.snackbar.message = 'No data found!'
          } else {
            this.allproblems_details = []
            this.allproblems.data.forEach((el) => {
              this.allproblems_details.push(el)
            })
          }
        } catch (error) {
          console.log(error)
        }
      }
    },
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
