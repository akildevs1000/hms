export default ({ $axios, store }, inject) => {

  $axios.setBaseURL('https://backend.myhotel2cloud.com/api/');

  if (process.env.LOCAL_IP == "local") {
    $axios.setBaseURL('https://hms-backend.test/api/');
  }

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
