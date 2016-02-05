//These three are exposed as global so they can be
//accessed on the main page.
React  = require("react");
ReactDOM = require("react-dom");
App = React.createClass({
    render: function() {
        return (
            <div className="app">
                <h1>Hello World!</h1>
                <div>{this.props.isLoggedIn ? 'logged in!' : 'nope'}</div>
            </div>
        );
    }
});