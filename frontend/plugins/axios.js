export default ({ $axios, store, $config }, inject) => {
  $axios.setBaseURL("https://backend.myhotel2cloud.com/api/");

  if (process.env.ENVIRONMENT == "development") {
    $axios.setBaseURL("http://127.0.0.1:8000/api/");
  }


  console.log("🚀 ~ $config.backendUrl:", $config.backendUrl)
  
  inject("backendUrl", $config.backendUrl);


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
