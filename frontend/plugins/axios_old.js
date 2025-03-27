export default ({ $axios, store }, inject) => {


    let backendURL = "https://backend.myhotel2cloud.com/api/";
    let appURL = "https://myhotel2cloud.com/";


    //   const isClient = typeof window !== "undefined";

    //   let backendURL = process.env.BACKEND_URL;
    //   let appURL = process.env.APP_URL;

    //   if (!process.env.BACKEND_URL) {
    //     backendURL = (isClient ? `http://${window.location.hostname || "localhost"}:8000/api` : "http://localhost:8000/api");
    //   }
    //   if (!process.env.APP_URL) {
    //     appURL = isClient ? `http://${window.location.hostname || "localhost"}:3001` : "http://localhost:3001";
    //   }

    inject("backendUrl", backendURL);
    inject("appUrl", appURL);

    $axios.onRequest(async (config) => {

        config.baseURL = backendURL; // Set backend API URL

        let user = store.state.auth.user;

        if (user) {
            config.params = {
                ...config.params,
                company_id: user.company_id,
            };
        }

        console.log("🚀 ~ Backend URL:", backendURL);
        console.log("🚀 ~ App URL:", appURL);

        return config; // Return the modified config
    });
};
