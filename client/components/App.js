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
        'isLoggedIn': React.PropTypes.bool,
        'githubClientId': React.PropTypes.string
    },

    render: function() {
        return (
            <div className="app">
                <AppHeader/>
                <div className="container main">
                    {this.props.isLoggedIn ? <Watcher/> : <LoginButtons/>}
                </div>
                <AppFooter/>
            </div>
        );
    }
});