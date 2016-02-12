var React = require("react");

module.exports = React.createClass({
    shouldComponentUpdate: function(newProps, newState) {
        return false; //No need to update this thing, it's static
    },

    render: function() {
        return (
            <header className="app-header">
                <nav className="pure-menu pure-menu-horizontal pure-menu-fixed">
                    <div className="container">
                        <a className="pure-menu-heading pull-left logo" href="/">
                            <span>CODE</span><i className="fa fa-flask"/><span>MANA</span>
                        </a>

                    </div>
                </nav>
            </header>
        );
    }
});
