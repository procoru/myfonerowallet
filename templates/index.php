<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MyFonero Wallet</title>
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
  <link href="http://unpkg.com/vuetify/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <style>
  </style>
</head>
<body>

<div id="app">
<v-app>

  <v-toolbar>
    <v-toolbar-title>MyFonero Wallet</v-toolbar-title>
  </v-toolbar>

	<v-content>
	<v-container v-if="!address">

<v-form v-model="valid">
    <v-text-field
      label="Login"
      v-model="login"
      :rules="loginRules"
      :counter="10"
      required
    ></v-text-field>
    <v-text-field
      label="Password"
      v-model="password"
      :rules="passwordRules"
      type="password"
      required
    ></v-text-field>

<v-btn
      @click="signIn"
      :disabled="!valid"
    >Enter in exists wallet</v-btn>

<v-btn
      @click="signUp"
      :disabled="!valid"
    >Create new wallet</v-btn>

  </v-form>

	</v-container>

	<v-container v-if="address">

	  <v-card color="blue-grey darken-2" class="white--text">
            <v-card-title primary-title>
              <div class="headline">Balance: {{ balance }} / Unlocked balance: {{ unlocked_balance }}</div>
              <div>Address: {{ address }}</div>
		<div style="width:100%">
 <v-text-field
      label="Amount"
      v-model="amount"
	dark
    ></v-text-field>
 <v-text-field
      label="Address"
      v-model="recipient"
	dark
    ></v-text-field>
		</div>
            </v-card-title>
            <v-card-actions>
              <v-btn color="primary" @click="send">Send</v-btn>
            </v-card-actions>
          </v-card>

	<v-card v-if="fee||tx_hash||tx_key" color="blue-grey darken-2" class="white--text">
		<v-card-title primary-title>
              		<div class="headline">Transaction info</div>
			<hr>
              		<div>Fee: {{ fee }}</div>
			<hr>
			<div>tx_hash: {{ tx_hash }}</div>
			<hr>
			<div>tx_key: {{ tx_key }}</div>
		</v-card-title>
	</v-card>

	<v-container>

	</v-content>

	<v-footer class="pa-3">
		<v-spacer></v-spacer>
		<div>© 2017 <a href="http://myfonero.mymsg.ru">myfonero.mymsg.ru</a></div>
	</v-footer>

</v-app>
</div>

<script src="http://unpkg.com/vue@2.5.9/dist/vue.min.js"></script>
<script src="http://unpkg.com/axios@0.17.1/dist/axios.min.js"></script>
<script src="http://unpkg.com/vuetify@0.17.3/dist/vuetify.min.js"></script>

<script>
new Vue({
        el: '#app',
	components: {
	},
	mounted: function() {
	},
	data: function() {
		return {
			valid: false,
		        login: '',
		        loginRules: [
		          (v) => !!v || 'Login is required',
		          (v) => v.length <= 10 || 'Login must be less than 10 characters'
		        ],
		        password: '',
		        passwordRules: [
		          (v) => !!v || 'Password is required',
		          (v) => v.length >= 3 || 'Password must be at least 3 characters'
		        ],
			address: '',
			balance: '',
			unlocked_balance: '',
			recipient: '',
			amount: '',
			fee: '',
			tx_hash: '',
			tx_key: ''
			// ...
		}
	},
	watch: {
	},
	computed: {
	},
	methods: {
		signIn: function() {
			axios.post('/login/' + this.login, {
				password: this.password
			}).then(function (response) {
                                if (response.data && response.data.status == 'ok') {
					this.address = response.data.address;
					this.balance = response.data.balance;
                                        this.unlocked_balance = response.data.unlocked_balance;
                                }
                        }.bind(this));
		},
		signUp: function() {
			axios.post('/registration/' + this.login, {
                                password: this.password
                        }).then(function (response) {
                                if (response.data && response.data.status == 'ok') {
					this.address = response.data.address;
					this.balance = response.data.balance;
					this.unlocked_balance = response.data.unlocked_balance;
                                }
                        }.bind(this));
		},
		send: function() {
			this.fee = '';
			this.tx_hash = '';
			this.tx_key = '';
			 axios.post('/send/' + this.login, {
                                password: this.password,
				recipient: this.recipient,
				amount: this.amount
				
                        }).then(function (response) {
                                if (response.data && response.data.status == 'ok') {
					this.fee = response.data.fee
					this.tx_hash = response.data.tx_hash
					this.tx_key = response.data.tx_key
                                }
                        }.bind(this));
		}
	}
})
</script>
</body>
</html>	
