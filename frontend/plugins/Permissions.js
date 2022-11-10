export default ({store}, inject) => {
	// Inject $hello(msg) in Vue, context and store.
	if(store && store.state.auth){
		let user = store.state.auth.user;
		inject('can', arg => user && (user.permissions.some(e => e.permission == arg) || user.master));
	}
  }