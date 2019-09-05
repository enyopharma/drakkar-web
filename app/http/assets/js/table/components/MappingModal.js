import React, { useState, useEffect } from 'react'
import Modal from 'react-bootstrap4-modal';

import api from '../api'
import Mapping from './Mapping'

const MappingModal = ({ description, close }) => {
    const [protein1, setProtein1] = useState(null)
    const [protein2, setProtein2] = useState(null)

    useEffect(() => {
        setProtein1(null)
        setProtein2(null)

        api.proteins.select(description.interactor1.protein.accession)
            .then(protein => setProtein1(protein))

        api.proteins.select(description.interactor2.protein.accession)
            .then(protein => setProtein2(protein))
    }, [description])

    return (
        <Modal visible={protein1 != null && protein2 != null} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping of description from {description.pmid}
                </h5>
                <button type="button" className="close" onClick={e => close()}>
                    &times;
                </button>
            </div>
            <div className="modal-body">
                <div className="row">
                    <div className="col">
                        {protein1 == null ? null : (
                            description.interactor1.mapping.length == 0 ? (
                                <p>No mapping on interactor 1</p>
                            ) : (
                                <Mapping
                                    type="h"
                                    name={description.interactor1.name}
                                    start={description.interactor1.start}
                                    stop={description.interactor1.stop}
                                    protein={protein1}
                                    mapping={description.interactor1.mapping}
                                />
                            )
                        )}
                    </div>
                    <div className="col">
                        {protein2 == null ? null : (
                            description.interactor2.mapping.length == 0 ? (
                                <p>No mapping on interactor 2</p>
                            ) : (
                                <Mapping
                                    type={description.type == 'hh' ? 'h' : 'v'}
                                    name={description.interactor2.name}
                                    start={description.interactor2.start}
                                    stop={description.interactor2.stop}
                                    protein={protein2}
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
