<?php

namespace App\Http\Controllers;

use App\Events\UpdateView;
use App\Helpers\Responses;
use App\Http\Requests\NewShift;
use App\Models\Person;
use App\Models\SurveySetting;
use App\Models\Shift;
use App\Models\StudentRegistration;
use App\Notifications\FeedbackRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function store(NewShift $request)
    {
        $idNumber = $request['identification'];
        $email = $request['email'];

        $person = StudentRegistration::where('dni', $idNumber)
            ->orWhere('email', $email)
            ->first();

        if (!$person) {
            $person = new StudentRegistration();
            $person->dni = $idNumber;
            $person->email = $email;
            $person->phone = $request['phone'];
            $person->is_puce = $request['isPuce'];
            $person->save();
        }

        $shift = new Shift();
        $shift->cubicle_id = $request['cubicle'];
        $shift->date = $request['date'];
        $shift->start_time = $request['start_time'];
        $shift->end_time = $request['end_time'];
        $shift->person_id = $person->id;
        $shift->status = true;
        $shift->save();

        return ['data' => $shift, 'message' => 'New shift created successfully'];
    }

    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);
            $shift->status = false;
            $shift->deleted_at = now()->toDateTimeString();
            $shift->update();

            return response()->json(['message' => 'Shift deleted successfully']);
        } catch (\Exception $e) {
           // return Responses::errorResponse('Error deleting shift', $e->getMessage());
        }
    }

    public function getUserShifts(Request $request)
    {
        try {
            $shifts = Shift::where('cubicle_id', $request['cubicle'])
                ->where('date', '>=', now())
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            return $shifts;
        } catch (\Exception $e) {
           // return Responses::errorResponse('Error retrieving shifts', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
      /*  try {
            $shift = Shift::findOrFail($request->shift);

            if ($request->status == 3 && $shift->status == 1) {
                $surveyHash = md5($shift->id . $shift->person_id);
                $surveySetting = SurveySetting::first();

                if ($surveySetting && $surveySetting->survey_enabled) {
                    $surveyUrl = $surveySetting->survey_type === 'system_survey'
                        ? env('APP_URL') . "/survey/" . $surveyHash
                        : $surveySetting->survey_link;

                    $shift->person->notify(new FeedbackRequestNotification($shift, $surveyUrl));
                }
            }

            $shift->status = $request->status;
            $shift->update();

            event(new UpdateView($shift));

            return response()->json(['message' => 'Shift updated successfully']);
        } catch (\Exception $e) {
            return Responses::errorResponse('Error updating shift', $e->getMessage());
        }*/
    }

    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        $date = $request->date ? Carbon::parse($request->date) : now();

        $dateUTC = $date->copy()->setTimezone('UTC');
        $time = $dateUTC->toTimeString();
        $date = $dateUTC->toDateString();

        $assignedShifts = Shift::join("cubicles", 'cubicle_id', '=', 'id')
            ->leftJoin("people", 'person_id', '=', 'id')
            ->where("user_id", $userId)
            ->where("date", $date)
            ->where("status", 1)
            ->whereNotNull("person_id")
            ->whereTime("end_time", ">=", $time)
            ->select(
                "shifts.id",
                "start_time",
                "end_time",
                "cubicles.name",
                "people.dni",
                "people.name",
                "people.email",
                "people.phone",
                "status"
            )
            ->orderBy("start_time")
            ->get();

        $modifiedShifts = Shift::join("cubicles", 'cubicle_id', '=', 'id')
            ->leftJoin("people", 'person_id', '=', 'id')
            ->where("user_id", $userId)
            ->where("date", $date)
            ->where("status", '!=', 1)
            ->select(
                "shifts.id",
                "start_time",
                "end_time",
                "cubicles.name",
                "people.dni",
                "people.name",
                "people.email",
                "people.phone",
                "status"
            )
            ->orderBy("start_time", "desc")
            ->get();

        return ['assigned' => $assignedShifts, 'modified' => $modifiedShifts];
    }
}
