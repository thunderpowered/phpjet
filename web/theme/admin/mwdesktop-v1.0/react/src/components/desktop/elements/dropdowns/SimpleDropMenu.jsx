import React, {Component} from 'react';

export class SimpleDropMenu extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        let {active, mouse} = this.props;
        return <div className={'Desktop__Elements__DropDowns--SimpleDropMenu position-fixed'}
                    style={{
                        'top': mouse.top,
                        'left': mouse.left,
                        'display': active ? 'block' : 'none'
                    }}>
            {React.Children.map(this.props.children, (child, index) => (
                <div className={'w-100 Desktop__Elements__DropDowns--SimpleDropMenu-Item-wrapper d-flex'}>
                    {React.cloneElement(child, {
                        ...child.props,
                        className: (child.props.className ? child.props.className : '') + ` w-100 text-left d-block Desktop__Elements__DropDowns--SimpleDropMenu-Item theme__background-color2 ${this.props.hoverClass} theme__text-color theme__border-bottom theme__border-color`,
                    })}
                </div>
            ))}
        </div>
    }
}