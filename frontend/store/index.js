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
  devices_logs_id: "",

  hotelQRCodeOTPverified: false,
  hotelQrcodeRequestId: "",
  hotelQrcodeCompanyId: "",
  hotelQrcodeRoomId: "",
  hotelQrcodeRoomNumber: "",
  hotelQrcodeWhatsappNumber: "",
  hotelQrcodeID: "",
  hotelQRCodeCartItems: [],
  timingName: "",
});

// contains your mutations
export const mutations = {
  timingName(state, value) {
    state.timingName = value;
  },
  hotelQRCodeCartItems(state, value) {
    state.hotelQRCodeCartItems = value;
  },
  hotelQRCodeOTPverified(state, value) {
    state.hotelQRCodeOTPverified = value;
  },
  hotelQrcodeWhatsappNumber(state, value) {
    state.hotelQrcodeWhatsappNumber = value;
  },
  hotelQrcodeID(state, value) {
    state.hotelQrcodeID = value;
  },
  hotelQrcodeRequestId(state, value) {
    state.hotelQrcodeRequestId = value;
  },
  hotelQrcodeCompanyId(state, value) {
    state.hotelQrcodeCompanyId = value;
  },
  hotelQrcodeRoomId(state, value) {
    state.hotelQrcodeRoomId = value;
  },
  hotelQrcodeRoomNumber(state, value) {
    state.hotelQrcodeRoomNumber = value;
  },
  customerImage(state, value) {
    state.customerImage = value;
  },
  devices_logs_id(state, value) {
    state.devices_logs_id = value;
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
