export const state = () => ({
  username: 'Guest',
  counter: 0,
  loggedin: false,
})

export const mutations = {
  increment(state) {
    state.counter++
  },
}
