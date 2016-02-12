var React = require("react");

module.exports = React.createClass({
    shouldComponentUpdate: function(newProps, newState) {
        return false; //No need to update this thing, it's static
    },

    render: function() {
        return (
            <footer>
                <p className="container">&copy; Jim Saunders</p>
            </footer>
        );
    }
});
