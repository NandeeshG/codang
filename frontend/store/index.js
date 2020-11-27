export const state = () => ({
  username: 'Guest',
  counter: 0,
  loggedIn: false,
})

export const mutations = {
  increment(state) {
    state.counter++
  },
}
