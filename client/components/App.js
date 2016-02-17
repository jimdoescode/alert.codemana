//These three are exposed as global so they can be
//accessed on the main page.
React  = require("react");
ReactDOM = require("react-dom");

var AppHeader = require("./AppHeader.js");
var AppFooter = require("./AppFooter.js");
var Watcher = require("./Watcher.js");
var LoginButtons = require("./LoginButtons.js");

App = React.createClass({
    propTypes: {
        'githubClientId': React.PropTypes.string
    },

    getURLParameter: function(name) {
        return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
    },

    getInitialState: function() {
        return {
            "isLoggedIn": false,
            "githubStateParam": null
        };
    },

    attemptGitHubLogin: function() {
        var code = this.getURLParameter('code');
        var state = this.getURLParameter('state');
        if (code === null) {
            var githubState = Math.random().toString(36).slice(2);
            this.setState({"githubStateParam": githubState});
            localStorage.setItem('githubState', githubState);
        } else if (state !== null){
            var url = '/github/login';
            var self = this;
            this.setState({"githubStateParam": state});
            window.fetch(url + '?code='+code+'&state='+state).then(function(response) {
                return response.json()
            }).then(function(json) {
                self.setState({
                    'isLoggedIn': true,
                    'user': json['user'],
                    'token': json['token']
                });
                console.log(json);
            });
        }
    },

    componentWillMount: function() {
        this.attemptGitHubLogin();
    },

    render: function() {
        var loginButtons = <LoginButtons githubClientId={this.props.githubClientId} githubStateParam={this.state.githubStateParam}/>;
        return (
            <div className="app">
                <AppHeader/>
                <div className="container main">
                    {this.state.isLoggedIn ? <Watcher/> : loginButtons}
                </div>
                <AppFooter/>
            </div>
        );
    }
});