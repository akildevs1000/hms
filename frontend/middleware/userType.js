const data = async ({ $auth, redirect }) => {
  console.log("User type", $auth);

  // let userType = {
  //   user: "/alarm/intruderdashboard",
  //   company: "/alarm/intruderdashboard",
  //   customer: "/customer/dashboard",
  //   //security: "/security/dashboard",
  //   security: "/operator/dashboard",
  //   technician: "/technician/dashboard",
  //   //operator: "/technician/dashboard",
  // };
  // if ($auth.user.user_type === "master" || $auth.user.is_master === true) {
  //   return redirect("/master");
  // }
  // if (!$auth.user || !$auth.user.user_type) {
  //   return redirect("/master"); // Fallback in case of null values
  // }
  // console.log(userType[$auth.user.user_type]);
  // if ($auth.user.user_type === "master" || $auth.user.is_master === true) {
  //   return redirect("/master");
  // }
  // redirect(userType[$auth.user.user_type] || "/master");
};
export default data;
