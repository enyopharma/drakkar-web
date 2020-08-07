import React from 'react'
import Modal from 'react-bootstrap4-modal';
import { FaEraser } from 'react-icons/fa'

type Props = {
    top: string
    show: boolean
    reset: () => void
    close: () => void
}

export const ResetModal: React.FC<Props> = ({ top, show, reset, close }) => {
    const resetAndScrollTop = () => {
        reset()
        close()
        document.getElementById(top)?.scrollIntoView()
    }

    return (
        <Modal visible={show} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Reseting form data
                </h5>
                <button type="button" className="close" onClick={e => close()}>
                    &times;
                </button>
            </div>
            <div className="modal-body">
                Are you sure you want to reset the form data?
            </div>
            <div className="modal-footer">
                <button
                    type="button"
                    className="btn btn-block btn-primary"
                    onClick={e => resetAndScrollTop()}
                >
                    <FaEraser /> Reset form data
                </button>
            </div>
        </Modal>
    )
}
