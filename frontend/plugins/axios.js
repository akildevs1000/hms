export default ({ $axios, store }, inject) => {
  // Override baseURL dynamically
  $axios.setBaseURL(process.env.BACKEND_URL);

  $axios.onRequest(async (config) => {
    let user = store.state.auth.user;

    if (user) {
      config.params = {
        ...config.params,
        company_id: user.company_id,
      };
    }

    return config;
  });
};
