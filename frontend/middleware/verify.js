const data = (req,res,next) => {

    console.log('francis');

//     if(ctx.store.$auth.user && ctx.store.$auth.user.user_type){

//     let user_type = ctx.store.$auth.user.user_type;


//     let restricted = [];

//     if(user_type == 'User'){
//         restricted = ['/inspire'];
//     }
//     else if(user_type == 'Security Guard'){
//         restricted = ['/inspire'];
//     }
//     else{
//         restricted = [];
//     }

//     let path = ctx.route.path;

//     let check = restricted.some(e => e != path);
//     if(check && path != '/')  ctx.redirect('/')

// }

}

export default data
