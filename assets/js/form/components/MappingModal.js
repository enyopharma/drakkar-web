import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

import MappingImg from './MappingImg';

const indexes = alignment => {
    const indexes = []

    alignment.results.map((r, i) => {
        r.occurences.map((o, j) => indexes.push([i, j]))
    })

    return indexes
}

const filteredAlignment = (alignment, selected) => {
    return Object.assign({}, alignment, {
        results: alignment.results.map((result, i) => {
            return Object.assign({}, result, {
                occurences: result.occurences.filter((o, j) => {
                    return selected.filter(s => s[0] == i && s[1] == j).length == 1
                })
            })
        }).filter(result => result.occurences.length > 0)
    })
}

const MappingModal = ({ type, width, subjects, alignment, save, close }) => {
    const [selected, setSelected] = useState(indexes(alignment))

    const filtered = filteredAlignment(alignment, selected)

    const isValid = filtered.results.length > 0

    const select = (i, j) => {
        setSelected(Array.concat([], selected, [[i, j]]))
    }

    const unselect = (i, j) => {
        setSelected(selected.filter(s => s[0] != i || s[1] != j))
    }

    const isIsoformSelected = i => {
        return selected.filter(s => s[0] == i).length > 0
    }

    const isOccurenceSelected = (i, j) => {
        return selected.filter(s => s[0] == i && s[1] == j).length > 0
    }

    const handleToggle = (e, i, j) => {
        e.target.checked ? select(i, j) : unselect(i, j)
    }

    return (
        <Modal visible={true} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping result
                </h5>
                <button type="button" className="close" onClick={close}>
                    &times;
                </button>
            </div>
            <div className="modal-body">
                <p>
                    <label>Mapped sequence</label>
                    <input
                        type="text"
                        className="form-control"
                        value={alignment.sequence}
                        readOnly
                    />
                </p>
                <ul className="list-unstyled">
                    {alignment.results.map((result, i) => (
                        <li key={i}>
                            <h4>{result.accession}</h4>
                            <div className="row">
                                <div className="col-11">
                                    <MappingImg
                                        type={type}
                                        start={1}
                                        stop={subjects[result.accession].length}
                                        width={width}
                                        active={isIsoformSelected(i)}
                                    />
                                </div>
                            </div>
                            {result.occurences.length == 0 ? (
                                <p>
                                    No alignment of the sequence on this isoform.
                                </p>
                            ) : (
                                <ul className="list-unstyled">
                                    {result.occurences.map((occurence, j) => (
                                        <li key={j}>
                                            <div className="row">
                                                <div className="col-11">
                                                    <MappingImg
                                                        type={type}
                                                        start={occurence.start}
                                                        stop={occurence.stop}
                                                        width={width}
                                                        active={isOccurenceSelected(i, j)}
                                                    />
                                                </div>
                                                <div className="col">
                                                    <input
                                                        type="checkbox"
                                                        checked={isOccurenceSelected(i, j)}
                                                        onChange={e => handleToggle(e, i, j)}
                                                    />
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </li>
                    ))}
                </ul>
            </div>
            <div className="modal-footer">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    disabled={! isValid}
                    onClick={e => save(filtered)}
                >
                    Save selected
                </button>
            </div>
        </Modal>
    )
}

export default MappingModal
