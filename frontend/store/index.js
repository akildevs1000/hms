// holds your root state
export const state = () => ({
  first_login: 1,
  color: "primary",
  employee_id: "",
  reservation: "",
  dataToSend: "",
  booking_payload: "",
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
  },
  reservation(state, value) {
    state.reservation = value;
    // console.log(state.reservation);
    // console.log(state);
  },
  booking_payload(state, value) {
    state.booking_payload = value;
    // console.log(state.reservation);
    // console.log(state);
  },
  setDataToSend(state, data) {
    state.dataToSend = data;
  },
};
export const actions = {
  setData({ commit }, data) {
    commit("setDataToSend", data);
  },
};

export const getters = {
  getDataToSend: (state) => state.dataToSend,
};
