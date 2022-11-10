// holds your root state
export const state = () => ({
  first_login: 1,
  color: "primary",
  employee_id: ""
});

// contains your mutations
export const mutations = {
  first_login(state, value) {
    state.first_login = value;
  },
  change_color(state, value) {
    state.color = value;
  },
  employee_id(state, value) {
    state.employee_id = value;
  }
};
