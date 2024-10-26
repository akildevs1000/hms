export const state = () => ({
    customer: null,
});

export const mutations = {
    SET_CUSTOMER(state, customer) {
        state.customer = customer;
    },
};

export const actions = {
    setCustomer({ commit }, customer) {
        commit('SET_CUSTOMER', customer);
    },
};

export const getters = {
    getCustomer(state) {
        return state.customer;
    },
};
