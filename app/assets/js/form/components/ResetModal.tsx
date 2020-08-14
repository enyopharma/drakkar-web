import React from 'react'
import Modal from 'react-bootstrap4-modal';
import { FaEraser } from 'react-icons/fa'

type Props = {
    show: boolean
    reset: () => void
    close: () => void
}

export const ResetModal: React.FC<Props> = ({ show, reset, close }) => (
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
                onClick={e => reset()}
            >
                <FaEraser /> Reset form data
            </button>
        </div>
    </Modal>
)
