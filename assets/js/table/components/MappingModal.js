import React, { useState, useEffect } from 'react'
import Modal from 'react-bootstrap4-modal';

import api from '../api'
import Mapping from './Mapping'

const MappingModal = ({ description, close }) => {
    const [isoforms1, setIsoforms1] = useState(null)
    const [isoforms2, setIsoforms2] = useState(null)

    useEffect(() => {
        setIsoforms1(null)
        setIsoforms2(null)

        api.proteins.select(description.interactor1.protein.accession)
            .then(protein => setIsoforms1(protein.isoforms))

        api.proteins.select(description.interactor2.protein.accession)
            .then(protein => setIsoforms2(protein.isoforms))
    }, [description])

    return (
        <Modal visible={isoforms1 != null && isoforms2 != null} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping of description from {description.pmid}
                </h5>
                <button type="button" className="close" onClick={e => close()}>
                    &times;
                </button>
            </div>
            <div className="modal-body">
                <div className="row my-0">
                    <div className="col">
                        {isoforms1 == null ? null : (
                            description.interactor1.mapping.length == 0 ? (
                                <p>No mapping on interactor 1</p>
                            ) : (
                                <Mapping
                                    type="h"
                                    start={description.interactor1.start}
                                    stop={description.interactor1.stop}
                                    isoforms={isoforms1}
                                    mapping={description.interactor1.mapping}
                                />
                            )
                        )}
                    </div>
                    <div className="col">
                        {isoforms2 == null ? null : (
                            description.interactor2.mapping.length == 0 ? (
                                <p>No mapping on interactor 2</p>
                            ) : (
                                <Mapping
                                    type={description.type == 'hh' ? 'h' : 'v'}
                                    start={description.interactor2.start}
                                    stop={description.interactor2.stop}
                                    isoforms={isoforms2}
                                    mapping={description.interactor2.mapping}
                                />
                            )
                        )}
                    </div>
                </div>
            </div>
        </Modal>
    )
}

export default MappingModal
