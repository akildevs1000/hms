// holds your root state
export const state = () => ({
  first_login: 1,
  color: "primary",
  employee_id: "",
  reservation: "",
  dataToSend: "",
  booking_payload: "",
  widget_data: "",
  widget_data_booking_confirmation: "",
  partyHallBookingCustomer: {},
  partyHallBookingEvents: {},
  partyHallBookingFood: {},
  partyHallBookingAmount: {},
  partyHallBookingExtra: {},
  customerImage: null,
  customerDocument: null,
});

// contains your mutations
export const mutations = {
  customerImage(state, value) {
    state.customerImage = value;
  },
  customerDocument(state, value) {
    state.customerDocument = value;
  },
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
  partyHallBookingCustomer(state, value) {
    state.partyHallBookingCustomer = value;
  },
  partyHallBookingExtra(state, value) {
    state.partyHallBookingExtra = value;
  },
  partyHallBookingEvents(state, value) {
    state.partyHallBookingEvents = value;
  },
  partyHallBookingFood(state, value) {
    state.partyHallBookingFood = value;
  },
  partyHallBookingAmount(state, value) {
    state.partyHallBookingAmount = value;
  },
  setDataToSend(state, data) {
    state.dataToSend = data;
  },
  widget_data(state, value) {
    state.widget_data = value;
  },

  widget_data_booking_confirmation(state, value) {
    state.widget_data_booking_confirmation = value;
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
