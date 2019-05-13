import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const MatureProteinEditor = ({ interactor, update, cancel }) => {
    const sequence = interactor.protein.sequence
    const matures = interactor.protein.matures

    const [name, setName] = useState(matures.length == 0 ? interactor.protein.name : '')
    const [start, setStart] = useState(matures.length == 0 ? 1 : '')
    const [stop, setStop] = useState(matures.length == 0 ? sequence.length : '')

    const isNameValid = matures.filter(m => m.name == name.trim()).length == 0

    const areCoordinatesValid = start == '' || stop == ''
        || (start <= stop && matures.filter(m => m.start == start && m.stop == stop).length == 0)

    const isMatureValid = name.trim() != ''
        && start != ''
        && stop != ''
        && isNameValid
        && areCoordinatesValid

    const selectMature = (mature) => {
        update(mature)
        cancel()
    }

    const setCoordinates = (start, stop) => {
        setStart(start)
        setStop(stop)
    }

    const handleValidate = () => {
        selectMature({name: name.trim(), start: start, stop: stop})
    }

    const handleReset = () => {
        setCoordinates(1, sequence.length)
    }

    return (
        <React.Fragment>
            {matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                <React.Fragment>
                    <p>
                        Existing sequences on this uniprot entry:
                    </p>
                    <div className="row">
                        <div className="col">
                            <MatureProteinList matures={matures} select={selectMature} />
                        </div>
                    </div>
                </React.Fragment>
            )}
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={start}
                        set={setStart}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={stop}
                        set={setStop}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleValidate}
                        disabled={! isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            <SubsequenceFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={handleReset}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default MatureProteinEditor;
