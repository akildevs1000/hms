export default ({ $axios, store }, inject) => {
  $axios.onRequest(async (config) => {
    let user = store.state.auth.user;

    if (user) {
      config.params = {
        ...config.params,
        company_id: user.company_id,
      };
    }

    return config; // Return the modified config
  });
};
