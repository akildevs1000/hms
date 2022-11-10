import colors from "vuetify/es5/util/colors";

export default {
    // Target: https://go.nuxtjs.dev/config-target
    target: "server",

    // Global page headers: https://go.nuxtjs.dev/config-head
    head: {
        titleTemplate: "",
        title: "Admin Panel",
        meta: [
            { charset: "utf-8" },
            { name: "viewport", content: "width=device-width, initial-scale=1" },
            { hid: "description", name: "description", content: "" },
            { name: "format-detection", content: "telephone=no" }
        ],

        link: [
            { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
          
            {
                rel: "stylesheet",
                href: "https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css"
            },
            {
                rel: "stylesheet",
                href: "https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons"
            },
            {
                rel: "stylesheet",
                href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css"
            },
            {
                rel: "stylesheet",
                href: "https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.4.95/css/materialdesignicons.min.css"
            }
        ]
    },

    // Global CSS: https://go.nuxtjs.dev/config-css
    css: ["~/assets/styles"],

    // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
    plugins: [
        { src: "~/plugins/axios.js" },
        { src: "~/plugins/TiptapVuetify", mode: "client" },
        { src : '~/plugins/vue-apexchart.js', ssr : false },
    ],

    // Auto import components: https://go.nuxtjs.dev/config-components
    components: true,

    // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
    buildModules: [
        // https://go.nuxtjs.dev/vuetify
        "@nuxtjs/vuetify",
        '@nuxtjs/dotenv',
    ],

    // Modules: https://go.nuxtjs.dev/config-modules
    modules: [
        // https://go.nuxtjs.dev/axios
        "@nuxtjs/axios",
        // https://go.nuxtjs.dev/pwa
        "@nuxtjs/pwa",
        "@nuxtjs/auth-next"
    ],

    // Axios module configuration: https://go.nuxtjs.dev/config-axios
    axios: {
        baseURL: process.env.BACKEND_URL,
    },

    auth: {
        strategies: {
            local: {
                endpoints: {
                    login: { url: "login", method: "post", propertyName: "token" },
                    logout: false,
                    user: { url: "me", method: "get", propertyName: "user" }
                },
                maxAge: 86400 // 24 hours
            }
        },

        redirect: {
            logout: "/login"
        }
    },

    router: {
        middleware: ["auth"]
    },

    // serverMiddleware: ['~middleware/verify.js'],

    // PWA module configuration: https://go.nuxtjs.dev/pwa
    pwa: {
        manifest: {
            lang: "en"
        }
    },

    // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
    vuetify: {
        customVariables: ["~/assets/variables.scss"],
        theme: {
            dark: false,
            themes: {
                light: {
                    primary: "#5fafa3",
                    accent: "#d8363a",
                    secondary: "#242424",
                    background: "#34444c",
                    info: colors.teal.lighten1,
                    warning: colors.amber.base,
                    error: colors.deepOrange.accent4,
                    success: colors.green.accent3,
                    main_bg: "#ECF0F4"
                },
                // dark: {
                //     // primary: "#fffff",
                //     // accent: "#d8363a",
                //     // secondary: "#242424",
                //     // background: "#34444c",
                //     // info: colors.teal.lighten1,
                //     // warning: colors.amber.base,
                //     // error: colors.deepOrange.accent4,
                //     // success: colors.green.accent3,
                //     // main_bg: "#ECF0F4"
                // }
            }
        }
    },

    // Build Configuration: https://go.nuxtjs.dev/config-build
    build: {
        transpile: ["vuetify/lib", "tiptap-vuetify","vue-apexchart"]
    },

    server: {
        host: process.env.LOCAL_IP,
        port: process.env.LOCAL_PORT,
    }
};
